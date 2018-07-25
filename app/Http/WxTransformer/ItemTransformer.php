<?php

namespace App\Http\WxTransformer;

use App\Models\Invoice;
use App\Models\Item;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        $data = $item->itemInfo();
        return [
            'item' => $data,
            'quotation' => $item->quotation ? $item->quotation->info() : null,
            'contract' => $item->contract,
            'evaluate' => $item->evaluate,
            'invoice' => Invoice::demandInvoiceLists($item->id),
        ];
    }

}