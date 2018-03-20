<?php

namespace App\Http\Transformer;

use App\Models\ItemUser;
use League\Fractal\TransformerAbstract;

class ItemUserTransformer extends TransformerAbstract
{
    /*
    id	int(10)	否
    user_id	int(5)	是		成员ID
    item_id	int(5)	是		项目ID
    level	int(5)	是	1	级别：1.成员；3.项目负责人；5.商务负责人；
    is_creator	tinyint(1)	是	0	是否是创建者: 0.否；1.是；
    type	tinyint(1)	是	1	类型;1.默认；
    status	tinyint(1)	是	1	状态：0.禁用；1.启用；
    */

    public function transform(ItemUser $itemUsers)
    {
        return [
            'id' => intval($itemUsers->id),
            'user_id' => intval($itemUsers->user_id),
            'item_id' => intval($itemUsers->item_id),
            'level' => intval($itemUsers->level),
            'is_creator' => intval($itemUsers->is_creator),
            'type' => intval($itemUsers->type),
            'status' => intval($itemUsers->status),
            'created_at' => $itemUsers->created_at,
        ];
    }

}
