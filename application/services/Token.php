<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Services;


use Commons\Helper\Str;
use Commons\Helper\Config;

class Token
{
    /**
     * 根据用户信息获取token
     */
    public function generateToken()
    {
        $randChar = Str::getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = Config::get('project.salt');
        return md5($randChar . $timestamp . $tokenSalt);
    }
}