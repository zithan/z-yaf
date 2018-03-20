<?php
/**
 * Created by PhpStorm.
 * Author: zithan
 */

namespace exceptions;

//自定义异常类的基类
class BaseException extends \Exception
{
    public $errcode = '-1';
    public $msg = '系统错误';
    public $status = 500;

    /**
     * 构造函数，接收一个关联数组
     * @param array $params 关联数组只应包含errcode、msg、status，且不为空
     */
    public function __construct($params=[])
    {
        if(!is_array($params)){
            return;
        }

        if(array_key_exists('errcode',$params)){
            $this->errcode = $params['errcode'];
        }

        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }

        if(array_key_exists('status',$params)){
            $this->status = $params['status'];
        }
    }
}