<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use Commons\Wechat\Wechat;
use exceptions\FailException;

class WechatController extends BaseController
{

    public function getSignAction()
    {
        $wechatConf = (new SettingModel())->getSetting('wechat_config');
        $wechatConfArr = $wechatConf ? json_decode($wechatConf, true) : [];

        if (!$wechatConfArr) {
            throw new FailException(['msg' => '服务器获取微信配置出错']);
        }

        $options = [
            'token' => $wechatConfArr['app_token'],
            'appid' => $wechatConfArr['app_id'],
            'appsecret' => $wechatConfArr['app_secret']
        ];

        $wechat = new Wechat($options);

        $appid = $options['appid'];
        $nonceStr = $wechat->generateNonceStr();
        $timestamp = time();
        $url = $this->getPost('url', '');

        $jsTicket = WechatModel::getJsTicket($appid);
        if (!$jsTicket) {
            $jsTicket = $wechat->getJsTicket();
            if (! WechatModel::setJsTicket($jsTicket, $appid)) {
                throw new FailException(['msg' => '服务器存储js_ticket失败']);
            }
        }

        $signPackage = $wechat->getJsSign($url, $timestamp, $nonceStr, $appid);
        //dump($signature);

        callbackAjax('sign', 200, $signPackage);
    }
}