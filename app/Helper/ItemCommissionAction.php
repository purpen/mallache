<?php

namespace App\Helper;


class ItemCommissionAction
{

    /**
     * 获取佣金数额
     *
     * @param $total_price float 项目总金额
     * @param null $design_user_id int 设计公司主账户ID
     * @return string
     */
    static function getCommission($total_price, $design_user_id = null)
    {
        $rate = config('constant.commission_rate');
        return number_format(bcmul($total_price, $rate, 2), 2, '.', '');
    }
}