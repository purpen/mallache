<?php

namespace App\Helper;


use App\Models\CommissionCount;
use App\Models\Item;

class ItemCommissionAction
{
    /**
     * 获取当前项目佣金数额
     *
     * @param Item $item
     * @return float
     */
    static function getCommission(Item $item)
    {
        $rate = ($item->commission_rate) / 100;
        return number_format(bcmul($item->price, $rate, 2), 2, '.', '');
    }


    /**
     * 获取设计公司佣金比例
     * @param $design_company_id
     * @return array ['rate' => 0, 'type' => $type]
     */
    static function getCommissionRate($design_company_id)
    {
        // 优惠类型
        $type = 0;
        // 判断是否有免佣金次数
        $com = CommissionCount::isPreferential($design_company_id);
        // 有免佣金次数
        if ($com) {
            $type = 1;
            // 次数减一
            $com->removeOne();

            return ['rate' => 0, 'type' => $type];
        }

        // 默认比例
        $rate = config('constant.commission_rate') * 100;
        return ['rate' => $rate, 'type' => $type];
    }
}