<?php
namespace Commons\Tool;

/**
 * @desc 数据验证
 */
class Validate
{
    /**
     * 判断手机格式
     * @param  string $val
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:32:14+080
     */
    public static function isMobile($val)
	{
		return preg_match('/^1[34578]\d{9}$/', $val);
	}
    
    public static function isTel($val)
    {
        return  preg_match('/^(0[0-9]{2,3}-)?([2-9][0-9]{6,7})+(-[0-9]{1,4})?$/', $val);
    }

    /**
     * 判断邮箱格式
     * @param  string $val
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:32:49+080
     */
	public static function isEmail($val)
	{
		return (bool)filter_var($val, FILTER_VALIDATE_EMAIL);
	}

    /**
     * 判断IP格式
     * @param  string $val
     * @param  integer $type
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:33:25+080
     */
	public static function isIp($val, $type = 0)
	{
		switch ($type)
		{
			case 1:
				return (bool)filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
				break;
			case 2:
				return (bool)filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
				break;
			default:
				//支持ipv4和ipv6
				return (bool)filter_var($val, FILTER_VALIDATE_IP);
				break;
		}

	}

    /**
     * 判断URL格式 必须添加http
     * @param  string $val
     * @param  integer $type
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:33:25+080
     */
	public static function isUrl($val, $type = 0)
	{
		switch ($type)
		{
			case 0:
				//要求 URL 是 RFC 兼容 URL。（比如：http://example）
				return (bool)filter_var($val, FILTER_VALIDATE_URL);
				break;
			case 1:
				//要求 URL 包含主机名（http://www.example.com）
				return (bool)filter_var($val, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
				break;
			case 2:
				//要求 URL 在主机名后存在路径（比如：eg.com/example1/）
				return (bool)filter_var($val, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
				break;
			case 3:
				//要求 URL 存在查询字符串（比如："eg.php?age=37"）
				return (bool)filter_var($val, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED);
				break;
			default:
				//要求 URL 是 RFC 兼容 URL。（比如：http://example）
				return (bool)filter_var($val, FILTER_VALIDATE_URL);
				break;
		}
	}

    /**
     * 判断浮点数格式
     * @param  string $val
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:35:11+080
     */
	public static function isFloat($val)
	{
		return filter_var($val, FILTER_VALIDATE_FLOAT) === false ? false : true;
	}

    /**
     * 判断整数格式
     * @param  string $val
     * @param  integer $min
     * @param  integer $max
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:36:29+080
     */
	public static function isInt($val, $min = null, $max = null)
	{
		if(is_null($min) && is_null($max))
		{
			return filter_var($val, FILTER_VALIDATE_INT) === false ? false : true;
		}else{
			$int_options = array("options"=>array("min_range"=>$min, "max_range"=>$max));

			return filter_var($val, FILTER_VALIDATE_INT, $int_options) === false ? false : true;
		}
	}

    /**
     * 判断布尔格式
     * @param  string $val
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:36:53+080
     */
	public static function isBoolean($val)
	{
		return filter_var($val, FILTER_VALIDATE_BOOLEAN) === false ? false : true;
	}

    /**
     * 判断小写字母格式
     * @param  string $val
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:37:26+080
     */
	public static function isLower($val)
	{
		return preg_match('/^[a-z]+$/', $val) ? true : false;
	}

    /**
     * 判断大写字母格式
     * @param  string $val
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:38:49+080
     */
	public static function isUpper($val)
	{
		return preg_match("/^[A-Z]+$/", $val) ? true : false;
	}

	/**
	 * 是否纯字母格式
	 * @param  string $val
	 * @return boolean
	 *
	 * @author hutong
	 * @date   2017-07-05T13:39:57+080
	 */
	public static function isAlpha($val)
	{
		return preg_match("/^[a-zA-Z]+$/", $val) ? true : false;
	}

    /**
	 * 是否只含有26个大小写英文字符和数字字符的字符串
	 * @param  string $val
	 * @return boolean
	 *
	 * @author hutong
	 * @date   2017-07-05T13:39:57+080
	 */
	public static function isAlnum($val)
	{
		return preg_match("/^[a-zA-Z\d]+$/", $val);
	}

    /**
     * 密码格式
     * @param  string $val
     * @param  integer $min
     * @param  integer $max
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:41:52+080
     */
	public static function isPassword($val, $min = 6, $max = 32)
    {
		return preg_match('/^[.a-zA-Z_0-9-!@#$%\^&*()]{' . $min . ','.$max.'}$/ui', $val) ? true : false;
	}

    /**
     * 昵称格式
     * @param  string $val
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:42:19+080
     */
	public static function isNickname($val)
    {
		return preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z_]{2,16}$/u", $val) ? true : false;
	}

    /**
     * 判断是否汉字
     * @param  string $val
     * @param  integer $type
     * @return boolean
     *
     * @author hutong
     * @date   2017-07-05T13:38:10+080
     */
	public static function isChinese($val, $type = 0)
	{
        if($type)
        {
            $preg = "/^[\x{4e00}-\x{9fa5}a-zA-Z_]+$/u";
        }else{
            $preg = "/^[\x{4e00}-\x{9fa5}]+$/u";
        }

		return preg_match($preg, $val) ? true : false;
	}

    /**
     * 表单数据验证
     * @param  array $vparams
     *         array(
     *              array('input' => '', 'require' => true, 'message' => '', 'validator' => 'isMobile'),
     *              array('input' => '', 'require' => true, 'message' => '', 'validator' => 'isEmail'),
     *              array('input' => '', 'require' => true, 'message' => '', 'validator' => 'isInt', 'min' => 2, 'max' => 50),
     *         )
     * @return array
     *
     * @author hutong
     * @date   2017-07-05T14:41:56+080
     */
    public static function form($vparams)
    {
        if(!is_array($vparams))
        {
            return false;
        }

        foreach($vparams as $k=>$v)
        {
            $v['validator'] = isset($v['validator']) ? trim($v['validator']) : '';

            if($v['require'] == "")
            {
				$v['require'] = false;
			}

            if($v['input'] == "" && $v['require'] == "true")
            {
				$vparams[$k]['result'] = false;
			}else{
				$vparams[$k]['result'] = true;
			}

            if($vparams[$k]['result'] && $v['input'] != "")
            {
                switch($v['validator'])
                {
					case "isMobile":
						$vparams[$k]['result'] = self::isMobile($v['input']);
						break;
                    case "isEmail":
                        $vparams[$k]['result'] = self::isEmail($v['input']);
                        break;
                    case "isIp":
                        $vparams[$k]['result'] = self::isIp($v['input'], isset($v['type']) ? (int)$v['type'] : 0);
                        break;
                    case "isUrl":
                        $vparams[$k]['result'] = self::isUrl($v['input'], isset($v['type']) ? (int)$v['type'] : 0);
                        break;
                    case "isFloat":
                        $vparams[$k]['result'] = self::isFloat($v['input']);
                        break;
                    case "isInt":
                        $vparams[$k]['result'] = self::isInt($v['input'], isset($v['min']) ? (int)$v['min'] : null, isset($v['max']) ? (int)$v['max'] : null);
                        break;
                    case "isBoolean":
                        $vparams[$k]['result'] = self::isBoolean($v['input']);
                        break;
                    case "isLower":
                        $vparams[$k]['result'] = self::isLower($v['input']);
                        break;
                    case "isUpper":
                        $vparams[$k]['result'] = self::isUpper($v['input']);
                        break;
                    case "isAlpha":
                        $vparams[$k]['result'] = self::isAlpha($v['input']);
                        break;
                    case "isAlnum":
                        $vparams[$k]['result'] = self::isAlnum($v['input']);
                        break;
                    case "isPassword":
                        $vparams[$k]['result'] = self::isPassword($v['input'], isset($v['min']) ? (int)$v['min'] : 6, isset($v['max']) ? (int)$v['max'] : 32);
                        break;
                    case "isNickname":
                        $vparams[$k]['result'] = self::isNickname($v['input']);
                        break;
                    case "isChinese":
                        $vparams[$k]['result'] = self::isChinese($v['input'], isset($v['type']) ? (int)$v['type'] : 0);
                        break;
					default:
                        if($v['input'] && $v['validator'])
                        {
                            $vparams[$k]['result'] = preg_match($v['validator'], $v['input']);
                        }else{
                            $vparams[$k]['result'] = true;
                        }
				}
            }
        }

        $error = array();
        foreach($vparams as $k=>$v)
        {
            if($v['result'] == false)
            {
                $error[] = $v['message'];
            }
        }

        unset($vparams);

        return $error;
    }
}
