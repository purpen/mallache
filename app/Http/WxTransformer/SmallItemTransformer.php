<?php

namespace App\Http\WxTransformer;

use App\Models\SmallItem;
use League\Fractal\TransformerAbstract;

class SmallItemTransformer extends TransformerAbstract
{
    public function transform(SmallItem $smallItem)
    {
        $s_user = substr($smallItem->user_name , 0 , 1);
        $start_phone = substr($smallItem->phone , 0 , 3);
        $end_phone = substr($smallItem->phone , 7);
        return [
            'id' => $smallItem->id,
            'status' => $smallItem->status,
            'item_name' => $smallItem->item_name,
            'user_name' => $smallItem->user_name,
            'phone' => $smallItem->phone,
            'created_at' => $smallItem->created_at,
            'new_user_name' => $s_user.'用户',
            'new_phone' => $start_phone.'****'.$end_phone,
        ];
    }

}