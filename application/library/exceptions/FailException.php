<?php
/**
 * Created by PhpStorm.
 * Author: zithan
 */

namespace exceptions;

/**
 * 失败
 */
class FailException extends BaseException
{
    public $msg = '操作失败';
    public $status = 400;
    public $errcode = 20004;
}