<?php
/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

namespace App\Validates;

class MoneyDetail extends BaseValidate
{
    protected $rule = [
        'pull_out_card_type' => 'require|isNotEmpty',
        'pull_out_card_num' => 'require|isNotEmpty',
        'pull_out_card_user' => 'require|isNotEmpty',
        'pull_out_card_bank' => 'require|isNotEmpty'
    ];

    protected $scene = [
        'pull_out' => ['pull_out_type', 'pull_out_username', 'pull_out_bank'],
    ];
}