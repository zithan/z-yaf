<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use Commons\Helper\Str;
use Commons\Helper\Cache;
use Gregwar\Captcha\CaptchaBuilder;
use Commons\Helper\Config;

// 图片验证器
class CaptchaController extends BaseController
{
    public function getAction()
    {
        $key = 'captcha_' . Str::getRandChar(18);

        //$params = $this->getPosts();
        //(new CaptchaValidate())->goCheck($params);

        $captcha = (new CaptchaBuilder(4))->build();
        //图片验证码2分钟过期
        $expire = 2;
        Cache::redis()->set($key, ['captcha_code' => $captcha->getPhrase()], $expire);

        $result = [
            'captcha_key' => $key,
            'expired_at' => time() + $expire,
            'captcha_image_content' => $captcha->inline()
        ];

        // 调试模式下，默认短信验证码返回前端，给予调试使用
        if (Config::get('debug.app.debug')) {
            $result['code'] = $captcha->getPhrase();
        }

        callbackAjax('图片验证码', 200, $result);
    }
}