<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */
/*
if (!defined('XJW'))
    exit(-1);*/

/*
 * 公用方法文件 请按格式新增方法
 * @author zithan@163.com
 *
 * if(!function_exists('exp')) {
 *     function exp()
 *     {
 *
 *     }
 * }
 *
 */

/**
 * 返回组合格式json格式数据
 */
if (!function_exists('callbackAjax')) {
    function callbackAjax($msg, $status = 200, $data = [], $errcode = 0)
    {
        $json = array(
            'msg'   => $msg,
            'status'=> (int)$status,
            'errcode'=> (int)$errcode,
            'result'=> $data
        );
        callbackJson($json);
    }
}


/**
 * 返回json格式数据
 * @author zithan@163.com
 */
if (!function_exists('callbackJson'))
{
    function callbackJson($arr = [])
    {
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * 优化var_dump()调试使用
 * @param mixed $var 变量
 * @param boolean browser是否浏览器输出
 * @return void|string
 */
if (!function_exists('dump')) {
    function dump($var, $browser = false)
    {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        if ($browser) {
            $output = '<pre>' . PHP_EOL . $output . '</pre>';
        }

        echo $output;
        exit;
    }
}
