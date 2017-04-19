<?php

namespace App\Http\Transformer;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /*
            id	    int(10)	    否
            type	tinyint(4)	否		类型：1.领域;2.
            name	varchar(20)	否		名称
            pid	    int(10)	    否		父ID
            status	tinyint(4)	否		状态
    */

    public function transform(Category $category)
    {
        return [
            'id' => intval($category->id),
            'type' => intval($category->type),
            'name' => strval($category->name),
            'pid' => intval($category->pid),
            'status' => intval($category->status),
        ];
    }
}