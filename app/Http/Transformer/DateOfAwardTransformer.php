<?php

namespace App\Http\Transformer;

use App\Models\DateOfAward;
use League\Fractal\TransformerAbstract;

class DateOfAwardTransformer extends TransformerAbstract
{
    public function transform(DateOfAward $dateOfAward)
    {
        return [
            'id' => $dateOfAward->id,
            'type' => $dateOfAward->type,
            'type_value' => $dateOfAward->type_value,
            'evt' => $dateOfAward->evt,
            'kind' => $dateOfAward->kind,
            'url' => $dateOfAward->url,
            'name' => $dateOfAward->name,
            'user_id' => $dateOfAward->user_id,
            'summary' => $dateOfAward->summary,
            'start_time' => $dateOfAward->start_time,
            'end_time' => $dateOfAward->end_time,
            'status' => $dateOfAward->status,
            'created_at' => $dateOfAward->created_at,
        ];
    }
}
