<?php

namespace App\Http\Transformer;

use App\Models\ItemRecommend;
use League\Fractal\TransformerAbstract;

class ItemDesignListTransformer extends TransformerAbstract
{
    public function transform(ItemRecommend $itemRecommend)
    {
        return [
            'status' => ($itemRecommend->itemStatus())['status'],
            'status_value' => ($itemRecommend->itemStatus())['status_value'],
            'item_status' => $itemRecommend->item_status,
            'item_status_value' => $itemRecommend->item_status_value,
            'design_company_status' => $itemRecommend->design_company_status,
            'design_company_status_value' => $itemRecommend->design_company_status_value,
            'design_company' => $itemRecommend->designCompany,
            'quotation' => $itemRecommend->quotation,
        ];
    }
}