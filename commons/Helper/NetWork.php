<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace Commons\Helper;


class NetWork
{
    /**
     * 提取访问IP
     *
     * @return [type] [description]
     * @author hutong
     * @date   2018-01-15T20:05:01+080
     */
    public static function getRealIp()
    {
        $ip = getenv('HTTP_X_REAL_IP');
        if($ip)
        {
            return $ip;
        }

        $ip = getenv('REMOTE_ADDR');

        return $ip;
    }
}