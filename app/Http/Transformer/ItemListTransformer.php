<?php

namespace App\Http\Transformer;

use App\Models\Item;
use App\Models\ItemRecommend;
use League\Fractal\TransformerAbstract;

class ItemListTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {

        return [
            'item' => $item->itemInfo(),
            'purpose_count' => ItemRecommend::purposeCount($item->id),
        ];
    }
}