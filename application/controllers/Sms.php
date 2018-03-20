<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use App\Validates\Sms as SmsValidate;
use Commons\Helper\Config;
use Commons\Helper\Cache;
use Commons\Helper\Str;
use exceptions\FailException;
use Commons\Tool\Ons;

class SmsController extends BaseController
{
    public function getCodeAction()
    {
        $params = $this->getPosts();

        (new SmsValidate())->scene('get_code')->goCheck($params);

        $captchaKey = $params['captcha_key'];
        $captchaData = Cache::redis()->get($captchaKey);

        if (!$captchaData) {
            throw new FailException(['msg' => '图片验证码失效']);
        }

        if (!hash_equals($captchaData['captcha_code'], $params['captcha_code'])) {
            throw new FailException(['msg' => '图片验证码错误']);
        }

        $username = $params['username'];

        $code = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);
        // 短信验证码发送交给阿里云队列
        $message = Ons::sendVerificationCode($username, $code);
        $this->addProducer($message);

        $key = 'smsCode_' . Str::getRandChar(18);
        // 短信验证码10分钟过期
        $expire = 10;
        Cache::redis()->set($key, ['username' => $username, 'sms_code' => $code], $expire);
        // 清除图片验证码的缓存
        Cache::redis()->del($captchaKey);

        $result = [
            'sms_code_key' => $key,
            'expired_at' => time() + $expire
        ];

        // 调试模式下，默认短信验证码返回前端，给予调试使用
        if (Config::get('debug.app.debug')) {
            $result['code'] = $code;
        }

        callbackAjax('短信验证码', 200, $result);
    }
}