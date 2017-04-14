<?php

namespace App\Http\Transformer;

use App\Models\Column;
use League\Fractal\TransformerAbstract;

class ColumnTransformer extends TransformerAbstract
{
    /*
        id	    int(10)	        否
        type	tinyint(4)	    否		栏目类型
        name	varchar(50)	    否		栏目名称
        content	varchar(200)	否		内容
        url	    varchar(200)	否		链接
        sort	int(10)	        否	0	排序
        status	tinyint(4)	    否		状态
    */

    public function transform(Column $column)
    {
        return [
            'id' => intval($column->id),
            'type' => intval($column->type),
            'name' => strval($column->name),
            'content' => strval($column->content),
            'url' => strval($column->url),
            'sort' => intval($column->sort),
            'status' => intval($column->status),
        ];
    }
}