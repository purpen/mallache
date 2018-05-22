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

    public function info()
    {
        /**
         * id    int(10)    否
         * design_user_id    int(10)    否        设计公司用户ID
         * item_id    int(10)    否        项目ID
         * price    tinyint(4)    否        项目金额
         * commission    decimal(10,2)            佣金
         * status
         */
        return [
            'id' => $this->id,
            'design_user_id' => $this->design_user_id,
            'item_id' => $this->item_id,
            'price' => $this->price,
            'commission' => $this->commission,
            'status' => $this->status,
        ];
    }

}
