<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace exceptions;

/**
 * Class ParameterException
 * 通用参数类异常错误
 */
class ParameterException extends BaseException
{
    public $msg = 'invalid parameters';
    public $status = 400;
    public $errcode = '20001';

}