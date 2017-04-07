<?php

namespace App\Http\Transformer;

use App\Models\DesignItemModel;
use League\Fractal\TransformerAbstract;

class DesignItemTransformer extends TransformerAbstract
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        good_field	    int(10)	        否		擅长领域 class_id
        project_cycle	tinyint(4)	    否		项目周期
        min_price	    decimal(10,2)	否		最低价格
        max_price	    decimal(10,2)	否		最高价格
    */

    public function transform(DesignItemModel $designItem)
    {
        return [
            'id' => intval($designItem->id),
            'user_id' => intval($designItem->user_id),
            'good_field' => strval($designItem->good_field),
            'project_cycle' => strval($designItem->project_cycle),
            'min_price' => strval($designItem->min_price),
            'max_price' => strval($designItem->max_price),
        ];
    }
}