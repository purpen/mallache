<?php

namespace App\Http\Transformer;

use App\Models\Tag;
use League\Fractal\TransformerAbstract;

class StatisticalTransformer extends TransformerAbstract
{
    public function transform($statistical)
    {
        return [
            'no_get' => intval($statistical->no_get),
            'no_stage' => intval($statistical->no_stage),
            'ok_stage' => intval($statistical->ok_stage),
            'overdue' => intval($statistical->overdue),
            'no_get_percentage' => strval($statistical->no_get_percentage),
            'no_stage_percentage' => strval($statistical->no_stage_percentage),
            'ok_stage_percentage' => strval($statistical->ok_stage_percentage),
            'overdue_percentage' => strval($statistical->overdue_percentage),
        ];
    }
}
