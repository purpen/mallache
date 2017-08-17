<?php

namespace App\Http\Transformer;

use App\Models\ItemStage;
use League\Fractal\TransformerAbstract;

class ItemStageTransformer extends TransformerAbstract
{
    /**
        id	                int(10)	        否
        item_id	            int(10)	        否		项目ID
        design_company_id	int(10)	        否		设计公司ID
        title	            varchar(50)	    否		阶段名称
        content	            varchar(500)	否		内容描述
        summary	            varcha(100)	    是	''	备注
        status              tinyint(4)	    否	0	是否发布：0.否；1.是;
     */
    public function transform(ItemStage $itemStage)
    {
        return [
            'id' => intval($itemStage->id),
            'item_id' => intval($itemStage->item_id),
            'design_company_id' => intval($itemStage->design_company_id),
            'title' => strval($itemStage->title),
            'content' => $itemStage->array_content,
            'summary' => strval($itemStage->summary),
            'item_stage_image' => $itemStage->item_stage_image,
            'status' => intval($itemStage->status),
            'created_at' => $itemStage->created_at,
            'percentage' => $itemStage->percentage,
            'amount' => $itemStage->amount,
            'time' => $itemStage->time,
            'confirm' => $itemStage->confirm,
            'sort' => $itemStage->sort,
        ];
    }

}