<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace Commons\Helper;

use Commons\Helper\Config;

class Image
{
    public static function getImagePath($path, $domain = '')
    {
        $imgDomain = empty(Config::get('project.imgdomain')) ? '' : Config::get('project.imgdomain');

        if($domain)
        {
            $imgDomain = $domain;
        }

        $path = $path ? $path : '/common/default.jpg';

        return $imgDomain.'/'.trim($path,'/');
    }
}