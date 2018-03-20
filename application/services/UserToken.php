<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Services;

use Commons\Helper\Cache;
use Commons\Helper\Config;
use Commons\Helper\NetWork;
use Commons\Helper\Image;
use exceptions\FailException;
use App\Services\User as UserService;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Commons\Helper\Str;

class UserToken extends Token
{
    /**
     * 用户登录后生成token
     * @param $username
     * @param $password
     * @return string
     * @throws FailException
     */
    public function loginByPwd($username, $password)
    {
        $user = UserService::checkLogin($username, $password);
        if(!$user) {
            throw new FailException(['msg' => '用户名或密码错误']);
        }

        $uid = $user['id'];
        // 生成token
        $token = $this->generateToken();
        $latestLoginIp = NetWork::getRealIp();
        $avatar = $user['avatar'];
        $avatar = empty($avatar) ? $avatar : Image::getImagePath($avatar);
        $data = [
            'uid' => $uid,
            'token' => $token,
            'nickname' => $user['nickname'],
            'invite_code' => $user['invite_code'],
            'avatar' => $avatar,
            'ip' => $latestLoginIp
        ];

        // 存储token缓存 @todo 将key创建为配置文件里
        Cache::redis()->set('token_promoters_user_' . $uid, $data, Config::get('project.tokenTime'));

        $dataAfterLogin = [
            'latest_login_time' => time(),
            'latest_login_ip' => $latestLoginIp
        ];

        // 推广员登录，每天只记一个活跃度
        $redisKey = sprintf("active_count_%s", date('Ymd'));
        $isLogin = Cache::redis()->getBit($redisKey, $uid);
        if (!$isLogin) {
            Cache::redis()->setBit($redisKey, $uid, 1);
            // @TODO 定时1小时执行
            \UserModel::setActiveById($uid);
        }

        // @TODO 队列
        // 生成推广员二维码和推广APP二维码
        if (empty($user['invite_qr_code'])) {
            $this->saveQrCode('invite_qr_code', $user['invite_code'], $uid);
        }

        if (empty($user['app_qr_code'])) {
            $this->saveQrCode('app_qr_code', $user['invite_code'], $uid);
        }

        // 更新登录信息
        \UserModel::updateAfterLogin($dataAfterLogin, $uid);

        return $data;
    }

    public function isLegalToken($aid, $token)
    {
        $redisKey = 'token_promoters_user_' . $aid;
        // 去服务器缓存找token
        $redisVal = Cache::redis()->get($redisKey);
        //在redis get不到值返回NULL
        if (empty($redisVal)) {
            throw new FailException(['msg' => 'token已过期请重新登录']);
        }

        //重新刷过期新时间
        Cache::redis()->expire($redisKey, Config::get('project.tokenTime'));

        if (!hash_equals($token, $redisVal['token'])) {
            throw new FailException(['msg'=>'无效token']);
        }

        return $redisVal;
    }

    public function destroyToken($uid)
    {
        // @todo 将key创建为配置文件里
        Cache::redis()->del('token_promoters_user_' . $uid);
    }

    /**
     * 生成二维码并上传阿里云
     * @param $visitUrl
     * @param $logoPath
     * @param $savePath
     * @return \Commons\Tool\booler
     */
    private function genQrCode($visitUrl, $logoPath, $savePath)
    {
        // Create a basic QR code
        $qrCode = new QrCode($visitUrl);
        $qrCode->setSize(300);

        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        //$qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER);
        $qrCode->setLogoPath($logoPath);
        $qrCode->setLogoWidth(80);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);

        // Directly output the QR code
        //header('Content-Type: '.$qrCode->getContentType());
        //echo $qrCode->writeString();

        // Save it to a file
        return $this->initOss()->putObject(Config::get('oss.default.bucket'), $savePath, $qrCode->writeString());
    }

    // $where = 'invite_qr_code';'app_qr_code';
    private function saveQrCode($where, $inviteUserCode, $uid)
    {
        $domain = '';
        switch ($where) {
            case 'invite_qr_code':
                $domain = Config::get('project.promoters.domain');
                break;
            case 'app_qr_code':
                $domain = Config::get('project.app.domain');
                break;
        }

        if (empty($domain)) {
            throw new FailException(['msg' => '二维码的域名不能为空']);
        }

        $visitUrl = $domain . '?invite_user_code=' . $inviteUserCode;
        $logoPath = APPLICATION_PATH . '/public/images/xjw_logo.png';
        $savePath = 'promoters/' . $where . '/'  . Str::getRandChar(6)  . $uid . '.png';
        if (!$this->genQrCode($visitUrl, $logoPath, $savePath)) {
            throw new FailException(['msg' => '生成二维码图片失败1~']);
        }

        if (!\UserModel::setFieldById($where, $savePath, $uid)) {
            throw new FailException(['msg' => '生成二维码图片失败2~']);
        }

        return true;
    }
}