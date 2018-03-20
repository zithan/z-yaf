<?php
/**
 * Created by PhpStorm.
 * Author: zithan
 */

namespace enum;

// 用户状态
class UserStateEnum
{
    // 待认证
    const VERIFYING = 1;

    // 已认证
    const VERIFIED = 2;

    // 已冻结
    const FROZEN = 3;
}