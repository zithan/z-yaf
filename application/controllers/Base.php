<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use Commons\Helper\Filter;
use Commons\Helper\NetWork;
use exceptions\FailException;
use App\Services\UserToken;

class BaseController extends \Yaf\Controller_Abstract
{
    protected $uid;

    public function init()
    {
        //@todo 做节流防止攻击

        // @todo 待抽离；配置不需要token的路由
        $notNeedToken = [
            'wechat/getSign',
            'token/get',
            'captcha/get',
            'sms/getCode',
            'user/register',
            'user/resetPwdBySms',
            'wechat/get',
            'user/getinviteqrcode',
            'user/getAppQrCode'
        ];
        if (in_array($this->getUri(), array_map('strtolower', $notNeedToken))) {
            return;
        }

        //aid
        $uid = $this->getPost('uid', '');
        //token
        $token = $this->getPost('token', '');

        if (empty($uid) || empty($token)) {
            throw new FailException(['msg'=>'缺少参数uid或参数token']);
        }

        //验证token合法性
        $data = (new UserToken())->isLegalToken($uid, $token);

        //SourcePolicy
        //OwnerPolicy

        $this->uid = $data['uid'];
    }

    protected function getPosts($filter = true)
    {
        $params = $this->getRequest()->getPost();

        if ($filter) {
            return Filter::xssClean($params);
        } else {
            return $params;
        }
    }

    protected function getUri()
    {
        $request = $this->getRequest();
        return strtolower($request->controller . '/' . $request->action);
    }
}
