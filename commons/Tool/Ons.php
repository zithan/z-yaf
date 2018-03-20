<?php
namespace Commons\Tool;

/**
 * 消息列队
 * array(
 *      'type' => '',
 *      'data' => '',
 * )
 */
class Ons
{
    private static function setData($type, $data)
    {
        $json = array(
            'type' => $type,
            'data' => $data
        );

        return json_encode($json);
    }

    /**
     * 短信验证码
     *
     * @param  [type] $mobile [description]
     * @param  [type] $code [description]
     * @return [type] [description]
     * @author hutong
     * @date   2018-01-09T11:01:52+080
     */
    public static function sendVerificationCode($mobile, $code)
    {
        $data = array(
            'mobile' => $mobile,
            'code' => $code,
        );

        return self::setData('sendVerificationCode', $data);
    }
}
