<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace Commons\Helper;


class Filter
{
    public static function xssClean($data)
    {
        if (is_array($data)) {
            return filter_var_array($data, FILTER_SANITIZE_STRING);
        } else {
            return filter_var($data, FILTER_SANITIZE_STRING);
        }
    }
}