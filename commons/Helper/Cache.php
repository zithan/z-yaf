<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace Commons\Helper;

class Cache
{
    //初始化cache
    private static function initCache()
    {
        $config = Config::get('cache');

        return new \HuTong\Cache\Storage($config);
    }

    public static function redis()
    {
        return self::initCache()->store('default');
    }

    public static function file()
    {
        return self::initCache()->store('file');
    }
}