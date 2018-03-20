<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use App\Services\MoneyDetail as MoneyDetailService;
use App\Validates\MoneyDetail as MoneyDetailValidate;
use exceptions\FailException;
use enum\MoneyDetailTypeEnum;
use enum\MoneyDetailStateEnum;

class MoneyDetailController extends BaseController
{
    public function pullOutAction()
    {
        $params = $this->getPosts();
        $moneyDetailValidate = (new MoneyDetailValidate());
        $moneyDetailValidate->goCheck($params);

        // 检查提取金额是否等于可提取金额
        $activeMoney = \UserModel::getActiveMoneyById($this->uid);
        //dump($activeMoney);
        if (!($activeMoney > 0)) {
            throw new FailException(['msg' => '提取金额失败']);
        }

        $data = $moneyDetailValidate->getDataByRule($params);

        $data['money'] = $activeMoney;
        $data['type'] = MoneyDetailTypeEnum::PULL_OUT;
        $data['state'] = MoneyDetailStateEnum::PULL_OUTING;
        $data['create_time'] = time();

        $result = MoneyDetailService::pullOutById($data, $this->uid);

        // +历史提现总额度
        //UserModel::IncHistoryPullOutById($activeMoney, $this->uid);

        if (!$result) {
            throw new FailException(['msg' => '提现失败']);
        }

        callbackAjax('提现成功，7个工作日到账');

    }
}