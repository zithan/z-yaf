<?php
/**
 * Created by PhpStorm.
 * Author: zithan
 */

namespace exceptions;

/**
 * 创建成功（如果不需要返回任何消息）
 * 200 表示创建/更新/删除成功
 */
class SuccessMessage extends BaseException
{
    public $msg = '操作成功';
    public $status = 200;
    public $errcode = 0;
}