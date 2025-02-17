<?php

namespace App\Http\Transformer;

use App\Models\ItemRecommend;
use League\Fractal\TransformerAbstract;

/**
 * 系统推荐给设计公司的项目列表
 *
 * Class DesignGetItemListTransformer
 * @package App\Http\Transformer
 */
class DesignGetItemListTransformer extends TransformerAbstract
{
    public function transform(ItemRecommend $itemRecommend)
    {
        return [
            'status' => ($itemRecommend->itemStatus())['status'],
            'status_value' => ($itemRecommend->itemStatus())['design_status_value'],
            'item_status' => $itemRecommend->item_status,
            'item_status_value' => $itemRecommend->item_status_value,
            'design_company_status' => $itemRecommend->design_company_status,
            'design_company_status_value' => $itemRecommend->design_company_status_value,
            'item' => $itemRecommend->item->itemInfo()
        ];
    }
}