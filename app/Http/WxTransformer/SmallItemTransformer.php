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
        ];
    }

}