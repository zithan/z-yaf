<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Validates;

class IDMustBeNonexistent extends BaseValidate
{
    protected $rule = [
        'id' => 'isNonexistent'
    ];
}
