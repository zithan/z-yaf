<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

/**
 * 获取和设置配置参数
 * @param string|array  $name 参数名
 * @param mixed         $value 参数值
 * @param string        $range 作用域
 * @return mixed
 */
function config($name = '')
{
    $config = \Yaf\Registry::get('config')->toArray();

    if (!strpos($name, '.')) {
        return isset($config[$name]) ? $config[$name] : null;
    } else {
        // 支持二、三维数组获取
        $name = explode('.', $name, 2);
        if (! strpos($name[1], '.')) {
            return isset($config[$name[0]][$name[1]]) ? $config[$name[0]][$name[1]] : null;
        } else {
            $name2 = $name[1];
            $name2 = explode('.', $name2, 2);
            return isset($config[$name[0]][$name2[0]][$name2[1]]) ? $config[$name[0]][$name2[0]][$name2[1]] : null;
        }

    }
}

