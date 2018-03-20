<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Services;

use enum\UserStateEnum;
use exceptions\FailException;

class User extends Base
{
    public static function checkLogin($username, $password)
    {
        $user = \Db::name('prmt_user')
            ->field('id,username,password,nickname,avatar,state,invite_code,invite_qr_code,app_qr_code')
            ->where('username', $username)->find();

        // 用户不存在
        if (empty($user)) {
            throw new FailException(['msg' => '用户名或密码错误']);
        }

        // 已锁定的推广员不允许登录
        if ($user['state'] == UserStateEnum::FROZEN) {
            throw new FailException(['msg' => '用户已被冻结']);
        }

        if (!password_verify($password, $user['password'])) {
            throw new FailException(['msg' => '用户名或密码错误']);
        }

        unset($user['password']);
        return $user;
    }
}