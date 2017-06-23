<?php

namespace App\Http\Transformer;

use App\Models\Item;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        $data = $item->itemInfo();
        return [
            'item' => $data,
            'quotation' => $item->quotation,
            'contract' => $item->contract,
            'evaluate' => $item->evaluate,
        ];
    }

}