<?php

namespace App\Http\Transformer;

use App\Models\ItemRecommend;
use League\Fractal\TransformerAbstract;

class ItemDesignListTransformer extends TransformerAbstract
{
    public function transform(ItemRecommend $itemRecommend)
    {
        return [
            'item_status' => $itemRecommend->item_status,
            'design_company_status' => $itemRecommend->design_company_status,
            'design_company' => $itemRecommend->designCompany,
            'quotation' => $itemRecommend->quotation,
        ];
    }
}