<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use App\Validates\Sms as SmsValidate;
use helper\Config;
use helper\Cache;
use helper\Str;
use exceptions\FailException;
use Overtrue\EasySms\EasySms;

class SmsController extends BaseController
{
    public function getCodeAction()
    {
        $params = $this->getPosts();

        (new SmsValidate())->scene('get_code')->goCheck($params);

        $captchaKey = $params['captcha_key'];
        $captchaData = Cache::redis()->get($captchaKey);

        if (!$captchaData) {
            throw new FailException(['msg' => '图片验证码失效']);
        }

        if (!hash_equals($captchaData['captcha_code'], $params['captcha_code'])) {
            throw new FailException(['msg' => '图片验证码错误']);
        }

        $code = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);
        // 短信验证码发送
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'yunpian',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'yunpian' => [
                    'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                ],
            ],
        ];

        $easySms = new EasySms($config);

        $easySms->send(13188888888, [
            'content'  => '您的验证码为: 6379',
            'template' => 'SMS_001',
            'data' => [
                'code' => 6379
            ],
        ]);


        $key = 'smsCode_' . Str::getRandChar(18);
        $expire = 600;
        Cache::redis()->setex(
            $key,
            $expire,
            json_encode(['username' => $params['username'], 'sms_code' => $code], JSON_UNESCAPED_UNICODE)
        );
        // 清除图片验证码的缓存
        Cache::redis()->del($captchaKey);

        $result = [
            'sms_code_key' => $key,
            'expired_at' => time() + $expire
        ];

        // 调试模式下，默认短信验证码返回前端，给予调试使用
        if (Config::get('debug.app.debug')) {
            $result['code'] = $code;
        }

        callbackAjax('短信验证码', 200, $result);
    }
}