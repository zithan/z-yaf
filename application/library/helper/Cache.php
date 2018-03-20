<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace helper;

use Predis\Client;

class Cache
{
    //初始化cache
    public static function redis()
    {
        //dump(Config::get('redis.default')->toArray());exit;
        //return new \Predis\Client(Config::get('redis')->default->toArray());
        return new Client(Config::get('redis.default')->toArray());
    }
}