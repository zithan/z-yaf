<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */
class WechatModel extends BaseModel
{
    public static function setAccessToken($accessToken, $appid = '')
    {
        //dump($accessToken . '====' . $appid);
        if (!$accessToken) return false;

        if ($appid) {
            $oldToken = Db::name('wechat_cache')->where('appid', $appid)->value('access_token');
            if ($oldToken) {
                $data = [
                    'access_token' => $accessToken,
                    'access_token_expires' => time() + 6000
                ];
                return Db::name('wechat_cache')->where('appid', $appid)->update($data);
            } else {
                $data = [
                    'appid' => $appid,
                    'access_token' => $accessToken,
                    'access_token_expires' => time() + 6000
                ];
                //dump($data);
                return Db::name('wechat_cache')->insert($data);
            }
        }

        return false;
    }

    public static function getAccessToken($appid)
    {
        $result = Db::name('wechat_cache')->field('access_token,access_token_expires')->where('appid', $appid)->find();

        if (!$result || $result['access_token_expires'] < (time() + 600)) {
            return false;
        }

        return $result;
    }

    public static function setJsTicket($jsTicket, $appid)
    {
        if (!$jsTicket) return false;

        if ($appid) {
            $id = Db::name('wechat_cache')->where('appid', $appid)->value('id');
            if (! $id) return false;

            $data = [
                'jsapi_ticket' => $jsTicket,
                'jsapi_ticket_expires' => time() + 6000
            ];
            return Db::name('wechat_cache')->where('appid', $appid)->update($data);

        }

        return false;
    }

    public static function getJsTicket($appid)
    {
        $result = Db::name('wechat_cache')->field('jsapi_ticket,jsapi_ticket_expires')->where('appid', $appid)->find();

        if (!$result || $result['jsapi_ticket_expires'] < (time() + 600)) {
            return false;
        }

        return $result;
    }
}