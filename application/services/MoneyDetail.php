<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Services;

class MoneyDetail extends Base
{
    public static function pullOutById($data, $userId)
    {
        \Db::startTrans();
        try {
            $result = \Db::name('prmt_money_detail')->insert($data);
            if (!$result) {
                throw new \Exception();
            }

            // 可提取金额清0
            \Db::name('prmt_user')->where('id', $userId)->setField('active_pull_out_money', 0);

            // +上历史提现总额度
            \Db::name('prmt_user')->where('id', $userId)->setInc('history_pull_out_total', $data['money']);

            //提交事务
            \Db::commit();

            return true;
        } catch (\Exception $e) {
            \Db::rollback();
            return false;
        }
    }

}