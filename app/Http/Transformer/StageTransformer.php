<?php

namespace App\Http\Transformer;

use App\Models\Stage;
use App\Models\TaskUser;
use League\Fractal\TransformerAbstract;

class StageTransformer extends TransformerAbstract
{
    /*
id	int(10)	否
item_id	int(11)	是		项目ID
title	varchar(100)	是		标题
    */

    public function transform(Stage $stage)
    {
        return [
            'id' => intval($stage->id),
            'item_id' => intval($stage->item_id),
            'title' => $stage->title,
            'created_at' => $stage->created_at,
        ];
    }
}
