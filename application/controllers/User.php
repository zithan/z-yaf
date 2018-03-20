<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use App\Validates\User as UserValidate;
use App\Validates\IDMustBePositiveInt;
use Commons\Helper\Cache;
use Commons\Helper\Str;
use Commons\Helper\Image;
use exceptions\FailException;
use enum\UserStateEnum;

class UserController extends BaseController
{
    /**
     * 用户注册
     * 1、用户输入手机号，请求图片验证码接口；
     * 2、服务器返回图片验证码；
     * 3、使用正确的图片验证码，请求短信验证码；
     * 4、服务器调用短信运营商的接口，发送短信至用户手机；
     * 5、通过正确的短信验证码，请求用户注册接口；
     * 6、完成注册流程
     */
    public function registerAction()
    {
        $params = $this->getPosts();

        $userValidate = new UserValidate();
        $userValidate->scene('register')->goCheck($params);

        $smsCodeKey = $params['sms_code_key'];
        $smsCodeData = Cache::redis()->get($smsCodeKey);

        if (!$smsCodeData) {
            throw new FailException(['msg'=>'短信验证码失效']);
        }

        if (!hash_equals($smsCodeData['sms_code'], $params['sms_code'])) {
            throw new FailException(['msg' => '短信验证码错误']);
        }

        // 注册必须有邀请人；检测邀请人是否存在和邀请人状态必须为已认证
        $invitedUser = UserModel::isOkForInviteCode($params['invite_user_code']);
        if (!$invitedUser) {
            throw new FailException(['msg' => '邀请人未认证或邀请码无效']);
        }

        if ($smsCodeData['username'] != $params['username']) {
            throw new FailException(['msg' => '手机号码前后不一致']);
        }

        // 检验手机号码是否注册
        $isRegistered = UserModel::isRegistered($params['username']);
        if ($isRegistered) {
            throw new FailException(['msg' => '手机号码已注册']);
        }

        $user = $userValidate->getDataByRule($params, ['username', 'password']);
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        // 标识为待审核
        $user['state'] = UserStateEnum::VERIFYING;
        // 生成邀请码：6位唯一和不可预测的字符串
        //@todo 方式1：定时任务：批量生成一批邀请码，然后分发，这样可不需要每次将检测是否重复
        $user['invite_code'] = Str::getRandChar(6);
        $user['create_time'] = time();
        $user['invited_user_id'] = $invitedUser['id'];

        // 新增用户信息
        $userId = UserModel::CreateOneByInvite($user);

        if (!$userId) {
            throw new FailException(['msg' => '注册失败']);
        }

        // @TODO 队列
        //$this->saveQrCode('invite_qr_code', $user['invite_code'], $userId);
        //$this->saveQrCode('app_qr_code', $user['invite_code'], $userId);

        // 清除短信验证码缓存
        Cache::redis()->del($smsCodeKey);

        callbackAjax('注册成功');

    }

    // 获取个人信息
    public function showByMeAction()
    {
        $result = UserModel::getById($this->uid);

        if (!$result) {
            throw new FailException(['msg' => '未获取到相关用户信息']);
        }

        callbackAjax('用户-详情信息', 200, $result);

    }

    public function showAction()
    {
        $params = $this->getPosts();
        (new IDMustBePositiveInt())->goCheck($params);

        $result = UserModel::getById($params['id']);

        if (!$result) {
            throw new FailException(['msg' => '未获取到相关用户信息']);
        }

        callbackAjax('用户-详情信息', 200, $result);
    }

    // 更新个人信息
    public function updateByMeAction()
    {
        $params = $this->getPosts();

        $userValidate = new UserValidate();
        //@TODO 不强求全部参数必填
        //$userValidate->scene('update')->goCheck($params);

        $data = $userValidate->getDataByRule($params, ['nickname', 'company', 'phone', 'email', 'qq_num']);

        if ($data) {
            $result = UserModel::updateOneById($data, $this->uid);
            if (!$result) {
                throw new FailException(['msg' => '更新个人信息失败']);
            }
        }

        callbackAjax('更新个人信息成功');

    }

    /**
     * 短信验证码方式重置密码
     * 1、用户输入手机号，请求图片验证码接口；
     * 2、服务器返回图片验证码；
     * 3、使用正确的图片验证码，请求短信验证码；
     * 4、服务器调用短信运营商的接口，发送短信至用户手机；
     * 5、通过正确的短信验证码，请求重置密码接口；
     * 6、完成重置密码流程
     */
    public function resetPwdBySmsAction()
    {
        $params = $this->getPosts();

        $userValidate = new UserValidate();
        $userValidate->scene('reset_pwd')->goCheck($params);

        $smsCodeKey = $params['sms_code_key'];
        $smsCodeData = Cache::redis()->get($smsCodeKey);

        if (!$smsCodeData) {
            throw new FailException(['msg'=>'短信验证码失效']);
        }

        if (!hash_equals($smsCodeData['sms_code'], $params['sms_code'])) {
            throw new FailException(['msg' => '短信验证码错误']);
        }

        $data = $userValidate->getDataByRule($params, ['password']);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if (!UserModel::updatePwdByUsername($data, $params['username'])) {
            throw new FailException(['msg' => '重置密码失败']);
        }

        // 清除短信验证码缓存
        Cache::redis()->del($smsCodeKey);

        callbackAjax('重置密码成功');

    }

    /**
     * 获取邀请人邀请码二维码
     * 数据库；如果有：二维码图片地址则返回；无：生成二维码图片，存数据库，返回图片地址
    */
    public function getInviteQrCodeAction()
    {
        $params = $this->getPosts();
        $userValidate = new UserValidate();
        $userValidate->scene('get_qr_code')->goCheck($params);

        $userModel = new UserModel();
        $qrCodeData = $userModel->getInviteQrCodeByCode($params['invite_user_code']);

        $qrCodeData['invite_qr_code'] = Image::getImagePath($qrCodeData['invite_qr_code']);

        callbackAjax('邀请推广员的二维码', 200, $qrCodeData);
    }

    // App安装邀请的二维码
    public function getAppQrCodeAction()
    {
        $params = $this->getPosts();
        $userValidate = new UserValidate();
        $userValidate->scene('get_qr_code')->goCheck($params);

        $userModel = new UserModel();
        $qrCodeData = $userModel->getAppQrCodeByCode($params['invite_user_code']);

        $qrCodeData['app_qr_code'] = Image::getImagePath($qrCodeData['app_qr_code']);

        callbackAjax('邀请安装APP的二维码', 200, $qrCodeData);

    }

    // 推广员的详情信息；订单数；认证数；可提成金额；冻结提成金额；注册日期；账号状态；邀请码；头像；手机；昵称
    public function detailAction()
    {

    }

    // 我的推广员；查询条件：活跃度；注册日期(区间：开始---结束，周期：一周，一个月，三个月...)；邀请码；认证数量；提成金额（排序）;
    public function getPromotersByMeAction()
    {
        $params = $this->getPosts(false);
        $page = isset($params['page']) ? $params['page'] : 1;
        $params['page'] = $page;

        $promoters = UserModel::getPromotersByInviteId($params, $this->uid);

        if (!$promoters) {
            $promoters = [];
        }

        callbackAjax('我的推广员-列表', 200, ['list' => $promoters]);
    }

    // 我的所有推广员的冻结金额总数
    public function getAllFrozenMoneyByMeAction()
    {
        $total = UserModel::getAllMoneyByInviteUserId($this->uid, false);

        $data['my_promoters_frozen_money'] = $total ?: 0;

        callbackAjax('我的所有推广员的冻结金额', 200, $data);
    }

    // 我的所有推广员的可提取金额总数
    public function getAllActiveMoneyByMeAction()
    {
        $total = UserModel::getAllMoneyByInviteUserId($this->uid, true);

        $data['my_promoters_active_money'] = $total ?: 0;

        callbackAjax('我的所有推广员的可提现金额', 200, $data);

    }

    // 设置我的推广员的状态
    public function setMyPromoterStateAction()
    {
        $params = $this->getPosts();
        (new UserValidate())->scene('set_state')->goCheck($params);

        $myPromoterId = $params['my_promoter_id'];
        $newState = $params['state'];

        // 获取当前的状态，比较要修改的状态，(注册)认证中1->(认证)已认证2->(锁定)已锁定3；(解锁)已认证2
        // @TODO 将设置状态的业务逻辑抽离到服务层
        $currentState = UserModel::getStateById($myPromoterId);

        switch ($currentState) {
            case 1:
                if ($newState != UserStateEnum::VERIFIED) $newState = 0;
                break;
            case 2:
                if ($newState != UserStateEnum::FROZEN) $newState = 0;
                break;
            case 3:
                if ($newState != UserStateEnum::VERIFIED) $newState = 0;
                break;
        }

        if ($newState == 0) throw new FailException(['msg' => '状态不可逆转']);

        $result = UserModel::setPromoterStateInId($newState, $myPromoterId, $this->uid);

        if (!$result) {
            throw new FailException(['msg' => '操作失败']);
        }

        callbackAjax('操作成功');
    }

    // @todo 抽离 我的客户到我的客户模块
    public function getCustomersByMeAction()
    {
        $params = $this->getPosts(false);

        $page = isset($params['page']) ? $params['page'] : 1;
        $params['page'] = $page;

        $customers = UserModel::getCustomersById($params, $this->uid);

        if (!$customers) {
            $customers = [];
        }

        foreach ($customers as &$customer) {
            $customer['dealer_avatar'] = Image::getImagePath($customer['dealer_avatar']);
        }

        $total = UserModel::getCustomerTotal($this->uid);

        callbackAjax('我的客户-列表', 200, ['list' => $customers, 'total' => $total]);
    }

}