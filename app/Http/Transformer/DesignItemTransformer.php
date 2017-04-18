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
        project_cycle	tinyint(4)	    否		项目周期
        min_price	    decimal(10,2)	否		最低价格
        max_price	    decimal(10,2)	否		最高价格
    */

    public function transform(DesignItemModel $designItem)
    {
        return [
            'id' => intval($designItem->id),
            'user_id' => intval($designItem->user_id),
            'type' => intval($designItem->type),
            'design_type' => intval($designItem->design_type),
            'project_cycle' => strval($designItem->project_cycle),
            'min_price' => strval($designItem->min_price),
            'max_price' => strval($designItem->max_price),
        ];
    }
}