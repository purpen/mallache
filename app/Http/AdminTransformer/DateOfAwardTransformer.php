<?php

namespace App\Http\AdminTransformer;

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
            'summary' => $dateOfAward->summary,
            'start_time' => $dateOfAward->start_time,
            'end_time' => $dateOfAward->end_time,
        ];
    }
}
