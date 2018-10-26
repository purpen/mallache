<?php

namespace App\Http\AdminTransformer;

use App\Models\SmallItem;
use League\Fractal\TransformerAbstract;

class AdminSmallItemListsTransformer extends TransformerAbstract
{
    public function transform(SmallItem $smallItem)
    {
        return [
            'id' => $smallItem->id,
            'status' => $smallItem->status,
            'item_name' => $smallItem->item_name,
            'user_name' => $smallItem->user_name,
            'phone' => $smallItem->phone,
            'is_ok' => $smallItem->is_ok,
            'summary' => $smallItem->summary,
            'created_at' => $smallItem->created_at,
        ];
    }
}