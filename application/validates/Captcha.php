<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Validates;

class Captcha extends BaseValidate
{
    protected $rule = [
        'username' => 'require|isMobile|isNotEmpty'
    ];
}