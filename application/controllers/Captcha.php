<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use Gregwar\Captcha\CaptchaBuilder;
use helper\Config;
use helper\Cache;
use helper\Str;

// 图片验证器
class CaptchaController extends BaseController
{
    public function getAction()
    {
        $key = 'captcha_' . Str::getRandChar(18);

        $captcha = (new CaptchaBuilder(4))->build();

        $expire =120;
        //dump(Cache::redis());
        Cache::redis()->setex(
            $key,
            $expire,
            json_encode(['captcha_code' => $captcha->getPhrase()], JSON_UNESCAPED_UNICODE)
        );

        $result = [
            'captcha_key' => $key,
            'expired_at' => time() + $expire,
            'captcha_image_content' => $captcha->inline()
        ];

        // 调试模式下，默认短信验证码返回前端，给予调试使用
        if (Config::get('project.debug')) {
            $result['code'] = $captcha->getPhrase();
        }

        callbackAjax('图片验证码', 200, $result);
    }
}