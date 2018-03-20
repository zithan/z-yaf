<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

use Commons\Helper\Config;
use enum\UserStateEnum;

class UserModel extends BaseModel
{
    public static function isRegistered($username)
    {
        return Db::name('prmt_user')->field('id')->where('username', $username)->find();
    }

    public static function isOkForInviteCode($inviteCode)
    {
        $map[] = ['invite_code', '=', $inviteCode];
        $map[] = ['state', '=', UserStateEnum::VERIFIED];
        return Db::name('prmt_user')->field('id')->where($map)->find();
    }

    public static function CreateOneByInvite($data)
    {
        return Db::name('prmt_user')->insertGetId($data);
    }

    public static function updateOneById($data, $id)
    {
        return Db::name('prmt_user')->where('id', $id)->update($data);
    }

    public static function updateAfterLogin($data, $id)
    {
        return Db::name('prmt_user')->where('id', $id)->update($data);
    }

    public static function updatePwdByUsername($data, $username)
    {
        return Db::name('prmt_user')->where('username', $username)->update($data);
    }

    public static function getById($id)
    {
        return Db::name('prmt_user')
            ->field('id,username,nickname,avatar,state,create_time,company,phone,email,qq_num,verified_count,
            active_pull_out_money,frozen_pull_out_money,history_pull_out_total,create_time,invite_code')
            ->where('id', $id)->find();
    }

    public static function getInviteQrCodeById($id)
    {
        return Db::name('prmt_user')->field('invite_qr_code,invite_code')->where('id', $id)->find();
    }

    public static function getInviteQrCodeByCode($inviteCode)
    {
        return Db::name('prmt_user')->field('invite_qr_code,invite_code')->where('invite_code', $inviteCode)->find();
    }

    public static function getAppQrCodeById($id)
    {
        return Db::name('prmt_user')->field('app_qr_code,invite_code')->where('id', $id)->find();
    }

    public static function getAppQrCodeByCode($inviteCode)
    {
        return Db::name('prmt_user')->field('app_qr_code,invite_code')->where('invite_code', $inviteCode)->find();
    }

    public static function setFieldById($field, $value, $id)
    {
        $result =  Db::name('prmt_user')->where('id', $id)->setField($field, $value);
        return ($result >= 0) ?: false;
    }

    private static function genConditions($params) {
        $map = [];
        if (isset($params['conditions'])) {
            $conditions = json_decode($params['conditions'], true);
            if (is_array($conditions) && ! empty($conditions)) {
                foreach ($conditions as $key => $condition) {
                    switch ($key) {
                        case 'register_time':
                            $map[] = ['create_time', 'between', $condition];
                            break;
                    }
                }
            }
        }

        return $map;
    }

    // conditions={"register_time":["注册开始时间","注册结束时间"]}
    public static function getPromotersByInviteId($params, $id, $pageSize = 0)
    {
        $map = self::genConditions($params);
        $map[] = ['invited_user_id', '=', $id];

        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $pageSize = $pageSize ?: Config::get('project.page.size');

        $orderField = isset($params['order_field']) ? $params['order_field'] : 'id';
        // 'nickname', 'invite_code', 'active_count', 'verified_count', 'create_time', 'active_pull_out_money', 'frozen_pull_out_money'
        if (!in_array($orderField, [
            'nickname', 'invite_code', 'active_count', 'verified_count', 'create_time', 'active_pull_out_money', 'frozen_pull_out_money'])) {
            $orderField = 'id';
        }
        $orderBy = (isset($params['order_by']) && (1 == $params['order_by'])) ? 'DESC' : 'ASC';

        $result = Db::name('prmt_user')
            ->field('id,avatar,username,nickname,state,create_time,active_count,verified_count,active_pull_out_money,frozen_pull_out_money')
            ->where($map)
            ->order($orderField, $orderBy)
            ->page($page, $pageSize)
            //->fetchSql(true)
            ->select();

        return $result;
    }

    public static function getCustomersById($params, $id, $pageSize = 0)
    {
        $map[] = ['pc.promoter_id', '=', $id];

        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $pageSize = $pageSize ?: Config::get('project.page.size');

        $result = Db::name('prmt_customer')
            ->alias('pc')
            ->field('di.fid,di.portrait as dealer_avatar,di.audit_status as dealer_state,di.contactName as dealer_name')
            ->join('dealer_info di', 'di.fid = pc.customer_id', 'LEFT')
            ->where($map)
            ->page($page, $pageSize)
            ->select();

        return $result;
    }

    public static function getCustomerTotal($id)
    {
        return Db::name('prmt_user')->getFieldById($id, 'customer_total');
    }

    public static function setPromoterStateInId($state, $promoteId, $id)
    {
        $map[] = ['invited_user_id', '=', $id];
        $map[] = ['id', '=', $promoteId];
        return Db::name('prmt_user')->where($map)->setField('state', $state);
    }

    public static function getActiveMoneyById($id)
    {
        return Db::name('prmt_user')->getFieldById($id, 'active_pull_out_money');
    }

    /**
     * @param $InviteUserId 邀请人id
     * @param bool $isActive 是：可提取金额；否：冻结金额
     * @return int|string
     */
    public static function getAllMoneyByInviteUserId($InviteUserId, $isActive = true)
    {
        $field = $isActive ? 'active_pull_out_money' : 'frozen_pull_out_money';
        return Db::name('prmt_user')->where('invited_user_id', $InviteUserId)->sum($field);
    }

    public static function IncHistoryPullOutById($money, $id)
    {
        return Db::name('prmt_user')->where('id', $id)->setInc('history_pull_out_total', $money);
    }

    public static function getStateById($id)
    {
        return Db::name('prmt_user')->where('id', $id)->value('state');
    }

    public static function setActiveById($id)
    {
        return Db::name('prmt_user')->where('id', $id)->setInc('active_count');
    }
}