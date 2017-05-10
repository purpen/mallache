<?php
namespace App\Http\Transformer;

use App\Models\Item;
use League\Fractal\TransformerAbstract;

class DesignItemListTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {

        return [
            'item' => $item->itemInfo(),
        ];
    }
}