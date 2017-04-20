<?php

namespace App\Http\Transformer;

use App\Models\DesignItemModel;
use League\Fractal\TransformerAbstract;

class DesignItemTransformer extends TransformerAbstract
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        type	        tinyint(4)	    否		设计类型：1.产品设计；2.UI UX 设计；
        design_type	    tinyint(4)	    是		设计类别：产品设计（1.产品策略；2.产品设计；3.结构设计；）UXUI设计（1.app设计；2.网页设计；）
        project_cycle	tinyint(4)	    否		项目周期  设计周期：1.1个月内；2.1-2个月；3.2个月；4.2-4个月；5.其他
        max_price	    decimal(10,2)	否		最高价格
    */

    public function transform(DesignItemModel $designItem)
    {
        return [
            'id' => intval($designItem->id),
            'user_id' => intval($designItem->user_id),
            'type' => $designItem->type,
            'type_val' => $designItem->type_val,
            'design_type' => $designItem->design_type,
            'design_type_val' => $designItem->design_type_val,
            'project_cycle' => $designItem->project_cycle,
            'project_cycle_val' => $designItem->project_cycle_val,
            'min_price' => floatval($designItem->min_price)
        ];
    }
}

