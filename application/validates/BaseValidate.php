<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Validates;

use exceptions\ParameterException;
use think\Validate;

//验证类的基类
class BaseValidate extends Validate
{
    /**
     * 检测所有客户端发来的参数是否符合验证类规则
     * 基类定义了很多自定义验证方法
     * 这些自定义验证方法其实，也可以直接调用
     */
    public function goCheck($params, $batch = false)
    {
        //过滤参数params
        //$params = Common::xssClean($params);

        //如果需要批量验证使用batch(true),批量验证如果验证不通过，返回的是一个错误信息的数组。
        if (! $this->batch($batch)->check($params)) {
            throw new ParameterException([
                'msg' => $this->getError(),
                'errcode' => 20002
            ]);
        }

        return true;
    }

    /**
     * @param array $arrays 传入变量数组
     * @param array $fields 字段
     * @param boolean $without 是否排除字段
     * @return array 按照规则key过滤后的变量数组
     * @throws ParameterException
     */
    public function getDataByRule($arrays, $fields = [], $without = false)
    {
        if (array_key_exists('create_user_id', $arrays) | array_key_exists('user_id', $arrays) |
            array_key_exists('create_time', $arrays)) {
            // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或者create_user_id或create_time'
            ]);
        }

        $newArray = [];
        $keys = $fields;
        if (empty($fields)) {
            $keys = array_keys($this->rule);
        }
        //dump($arrays);

        foreach ($keys as $key) {
            if ($without && in_array($key, $keys)) {
                continue;
            }

            if (!isset($arrays[$key])) {
                continue;
            }

            $newArray[$key] = $arrays[$key];

        }

        //dump($newArray);
        return $newArray;
    }

    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return $field . '必须是正整数';
    }

    protected function isNotEmpty($value, $rule = '', $data = '', $field = '')
    {
        if (empty($value)) {
            return $field . '不允许为空';
        } else {
            return true;
        }
    }

    protected function isNonexistent($value, $rule = '', $data = '', $field = '')
    {
        if (isset($value) && ! empty($value)) {
            return $field . '不可以传值';
        } else {
            return true;
        }
    }

    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|6|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}