<?php

namespace App\Http\Transformer;

use App\Models\DesignProject;
use League\Fractal\TransformerAbstract;

class DesignProjectStatisticalTransformer extends TransformerAbstract
{
    public function transform(DesignProject $designProducts)
    {
        return [
            'id' => (int)$designProducts->id,
            'level' => (int)$designProducts->level,
            'level_value' => $designProducts->level_value,
            'name' => $designProducts->name,
            'start_time' => $designProducts->start_time,
            'ok_stage_percentage' => (int)$designProducts['ok_stage_percentage'] ? (int)$designProducts['ok_stage_percentage'] : (int)0,
        ];
    }
}