<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

require_once realpath(__DIR__ . '/../') . '/vendor/autoload.php';

class Zithan
{
    public function test()
    {
        $faker = Faker\Factory::create('zh_CN');
        echo $faker->unixTime();
    }
}

(new Zithan())->test();