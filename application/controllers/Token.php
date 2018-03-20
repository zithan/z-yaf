<?php
/**
 * Created by PhpStorm.
 * Author: zithan
 */

use App\Validates\User as UserValidate;
use App\Services\UserToken;
use exceptions\TokenException;
use Commons\Helper\Cache;

class TokenController extends BaseController
{
    // 密码登录
    public function getAction()
    {
        $params = $this->getPosts();
        (new UserValidate())->scene('login')->goCheck($params);

        //验证用户信息获取token等信息
        $userInfo = (new UserToken())->loginByPwd($params['username'], $params['password']);

        callbackAjax('获取token成功', 200, $userInfo);
    }

    // 微信授权登录
    public function getByWxAction()
    {

    }

    public function destroyAction()
    {
        $redisKey = 'token_promoters_user_' . $this->uid;
        Cache::redis()->del($redisKey);

        callbackAjax('注销成功', 200);
    }
}
