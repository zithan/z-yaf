<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Validates;

class User extends BaseValidate
{
    protected $rule = [
        'username' => 'require|isMobile|isNotEmpty',
        'password' => 'require|isNotEmpty|min:6',
        'nickname' => 'require|isNotEmpty|max:20',
        'company' => 'require|isNotEmpty',
        'phone' => 'require|isNotEmpty',
        'email' => 'require|isNotEmpty|email',
        'qq_num' => 'require|isNotEmpty|number',
        'state' => 'require|isNotEmpty|number',
        'my_promoter_id' => 'require|isNotEmpty|number',  // 非数据库字段
        'invite_user_code' => 'require|isNotEmpty',  // 非数据库字段
        'sms_code_key' => 'require|isNotEmpty',  // 非数据库字段
        'sms_code' => 'require|isNotEmpty'  // 非数据库字段
    ];

    protected $scene = [
        'register' => ['username', 'password', 'invite_user_code', 'sms_code_key', 'sms_code'],
        'login' => ['username', 'password'],
        'reset_pwd' => ['username', 'password', 'sms_code_key', 'sms_code'],
        'update' => ['nickname', 'company', 'phone', 'email', 'qq_num'],
        'set_state' => ['state', 'my_promoter_id'],
        'get_qr_code' => ['invite_user_code']
    ];

    protected $message = [
        'invite_user_code' => '邀请码不能为空'
    ];
}