<?php

namespace App\Http\Transformer;

use App\Models\Tag;
use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{
    /*
    id	                int(10)	否
    title	            varchar(100)	是		名称
    user_id	            int(11)	        是		创建人ID
    item_id	            int(11)	        是		项目ID
    type	            int(11)	        是	    颜色
    */

    public function transform(Tag $tags)
    {
        return [
            'id' => intval($tags->id),
            'title' => strval($tags->title),
            'user_id' => intval($tags->user_id),
            'item_id' => intval($tags->item_id),
            'type' => intval($tags->type),
            'type_val' => strval($tags->type_val),
            'created_at' => $tags->created_at,
        ];
    }
}
