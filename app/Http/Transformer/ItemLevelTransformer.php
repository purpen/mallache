<?php

namespace App\Http\Transformer;

use App\Models\ItemLevel;
use League\Fractal\TransformerAbstract;

class ItemLevelTransformer extends TransformerAbstract
{
    /*
        id	int(10)	否
        name	varchar(20)	是		名称
        summary	varchar(500)	否		描述
        user_id	int(5)	是		创建人ID
        type	tinyint(1)	是	1	类型;1.默认；
        status	tinyint(1)	是	1	状态：0.禁用；1.启用；
    */

    public function transform(ItemLevel $itemLevels)
    {
        return [
            'id' => intval($itemLevels->id),
            'name' => strval($itemLevels->name),
            'summary' => $itemLevels->summary,
            'user_id' => intval($itemLevels->user_id),
            'type' => intval($itemLevels->type),
            'status' => intval($itemLevels->status),
            'created_at' => $itemLevels->created_at,
        ];
    }

}
