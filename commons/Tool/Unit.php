<?php
namespace Commons\Tool;

use Endroid\QrCode\QrCode;
class Unit
{
    /**
	 * 获取随机字符串
	 *
	 * @param  integer $num
	 * @param  integer $type 0.数字字母组合 1.数组 2.字母
	 * @return [type]
	 * @author hutong
	 * @date   2017-03-29T16:37:32+080
	 */
    public static function getRandomStr($num = 4, $type = 0)
    {
        $code = '';
	    $decimal = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,);
	    $letter = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	    if($type == 0)
	    {
	        $codes = array_merge($decimal, $letter);
	    }else if ($type == 1){
	        $codes = $decimal;
	    }else{
	        $codes = $letter;
	    }
	    for ($i = 0; $i < $num; $i++)
	    {
	        $code .= $codes[array_rand($codes)];
	    }
	    return $code;
    }

    /**
     * 获取密码统一加密方式
     *
     * @param  string $password
     * @param  string $salt
     * @return string
     * @author hutong
     * @date   2017-08-17T15:23:46+080
     */
    public static function getPassword($password, $salt)
    {
        return md5(md5($password).$salt);
    }

    public static function getOverTime($time)
    {
        $str = '';
        if($time < 0)
        {
            return $str;
        }
        if(($day = floor($time / 86400)))
        {
            $str .= $day."天";
        }

        if(($hour = floor(($time % 86400) / 3600)))
        {
            $str .= $hour."小时";
        }

        $str .= floor(($time % 3600) / 60)."分";

        return $str;
    }

    // 转换大小单位
    public static function getFormatBytes($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
	 * 生成全局唯一标识符
	 * @return string
	 *
	 * @author hutong
	 * @date   2017-07-04T11:31:55+080
	 */
	public static function guid()
	{
		if (function_exists('com_create_guid'))
		{
	        return com_create_guid();
	    } else {
	        mt_srand((double)microtime() * 10000);
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = substr($charid, 0, 8) . $hyphen
				  . substr($charid, 8, 4) . $hyphen
				  . substr($charid, 12, 4) . $hyphen
				  . substr($charid, 16, 4) . $hyphen
				  . substr($charid, 20, 12);
	        return $uuid;
	    }
	}

    /**
     * 生成二维码
     *
     * @param  [type] $val [description]
     * @return [type] [description]
     * @author hutong
     * @date   2017-12-01T16:38:55+080
     */
    public static function qrCode($text)
    {
        $qrCode = new QrCode($text);

        return 'data:image/png;base64,'.base64_encode($qrCode->writeString());
    }

    public static function getRealIp()
    {
        $real_ip = getenv('HTTP_X_REAL_IP');

        if($real_ip)
        {
            return $real_ip;
        }

        if(getenv('HTTP_X_FORWARDED_FOR'))
        {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }elseif(getenv('HTTP_CLIENT_IP')){
            $ip = getenv('HTTP_CLIENT_IP');
        }else{
            $ip = getenv('REMOTE_ADDR');
        }
        return $ip;
    }
}
