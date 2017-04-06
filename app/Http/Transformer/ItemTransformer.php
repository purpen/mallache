<?php

namespace App\Http\Transformer;

use App\Models\Item;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        return [
            'id' => $item->id,
            'design_type' => $item->design_type,
            'field' => $item->field,
        ];
    }
}