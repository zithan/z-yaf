<?php
/**
 * Created by PhpStorm.
 * Author: zithan
 */

namespace enum;

// 用户状态
class MoneyDetailStateEnum
{
    // 提现中
    const PULL_OUTING = 1;

    // 已提现
    const PULL_OUT_SUCCESS = 2;

    // 提现失败
    const PULL_OUT_FAIL = 3;
}