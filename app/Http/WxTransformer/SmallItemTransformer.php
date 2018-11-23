<?php

namespace App\Http\WxTransformer;

use App\Models\SmallItem;
use League\Fractal\TransformerAbstract;

class SmallItemTransformer extends TransformerAbstract
{
    public function transform(SmallItem $smallItem)
    {
        return [
            'id' => $smallItem->id,
            'status' => $smallItem->status,
            'item_name' => $smallItem->item_name,
            'user_name' => $smallItem->user_name,
            'phone' => $smallItem->phone,
            'created_at' => $smallItem->created_at,
            'new_user_name' => $smallItem->new_user_name,
            'new_phone' => $smallItem->new_phone,
        ];
    }

}