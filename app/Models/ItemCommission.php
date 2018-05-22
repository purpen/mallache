<?php

namespace App\Models;

class ItemCommission extends BaseModel
{
    protected $table = 'item_commission';

    /**
     * 创建平台项目佣金收取记录
     *
     * @param $item_id
     * @param $design_user_id
     * @param $price
     * @param $commission
     */
    public static function createCommission($item_id, $design_user_id, $price, $commission)
    {
        $item_commission = new ItemCommission();
        $item_commission->design_user_id = (int)$design_user_id;
        $item_commission->item_id = (int)$item_id;
        $item_commission->price = $price;
        $item_commission->commission = $commission;
        $item_commission->save();

    }
}
