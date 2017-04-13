<?php

namespace App\Http\Transformer;

use App\Models\DesignItemModel;
use League\Fractal\TransformerAbstract;

class DesignItemTransformer extends TransformerAbstract
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        design_type	    int(10)	        否		设计类型：1.产品策略；2.产品设计；3.结构设计；4.app设计；5.网页设计；
        project_cycle	tinyint(4)	    否		项目周期
        min_price	    decimal(10,2)	否		最低价格
        max_price	    decimal(10,2)	否		最高价格
    */

    public function transform(DesignItemModel $designItem)
    {
        return [
            'id' => intval($designItem->id),
            'user_id' => intval($designItem->user_id),
            'design_type' => strval($designItem->design_type),
            'project_cycle' => strval($designItem->project_cycle),
            'min_price' => strval($designItem->min_price),
            'max_price' => strval($designItem->max_price),
        ];
    }
}