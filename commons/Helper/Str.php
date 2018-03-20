<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace Commons\Helper;


class Str
{
    public static function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }

    /**
     * 生成全局唯一标识符
     * @return string
     *
     * @author hutong
     * @date   2017-07-04T11:31:55+080
     */
    public static function guid()
    {
        if (function_exists('com_create_guid'))
        {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
            return $uuid;
        }
    }

    /**
     * 密码加密处理
     * @param  [type] $password [description]
     * @param  [type] $salt [description]
     * @return [type] [description]
     * @author hutong
     * @date   2018-01-15T13:46:52+080
     */
    public static function getPassword($password, $salt)
    {
        $configs = \Yaf\Registry::get('config')->toArray();
        $psalt = isset($configs['project']['salt']) ? $configs['project']['salt']:'';

        return md5($psalt . md5($password) . $salt);
    }
}