<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Validates;

class Sms extends BaseValidate
{
    protected $rule = [
        'username' => 'require|isNotEmpty|number',
        'captcha_key' => 'require|isNotEmpty',
        'captcha_code' => 'require|isNotEmpty'
    ];

    protected $scene = [
        'get_code' => ['username', 'captcha_key', 'captcha_code'],
    ];
}