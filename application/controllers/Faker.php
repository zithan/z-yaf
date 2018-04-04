<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use \Faker\Factory;

class FakerController extends \Yaf\Controller_Abstract
{
    public function genToInviteCodeAction()
    {
        $faker = Factory::create('zh_CN');
        //dump(date('Y-m-d H:i:s', $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now')->getTimestamp()));
        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $data[] = [
                'mobile' => $faker->phoneNumber,
                'code' => $faker->regexify('[0-9][a-z][A-Z]{6}'),
                'state' => rand(1, 3),
                'aid' => $faker->randomElement([57, 84, 85]),
                'create_time' => date('Y-m-d H:i:s', $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now')->getTimestamp())
            ];
        }


        \Db::name('invite_code')->insertAll($data);

        die('生成数据成功');
    }
}