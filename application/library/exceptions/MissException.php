<?php
/**
 * Created by PhpStorm.
 * Author: zithan
 */

namespace exceptions;

/**
 * 404找不到资源，本系统设定为400
 */
class MissException extends BaseException
{
    public $msg = '未找到资源';
    public $status = 400;
    public $errcode = '20003';
}