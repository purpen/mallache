<?php

namespace App\Http\AdminTransformer;

use App\Models\Column;
use League\Fractal\TransformerAbstract;

class AdminColumnTransformer extends TransformerAbstract
{
    public function transform(Column $column)
    {
        return [
            'id' => $column->id,
            'type' => $column->type,
            'type_value' => $column->type_value,
            'title' => $column->title,
            'content' => $column->content,
            'url' => $column->url,
            'status' => $column->status,
            'cover_id' => $column->cover_id,
            'cover' => $column->cover,
            'image' => $column->image,
            'created_at' => $column->created_at,
        ];
    }
}