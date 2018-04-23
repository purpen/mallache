<?php

namespace App\Http\Transformer;

use App\Models\DesignStage;
use League\Fractal\TransformerAbstract;

class DesignStageTransformer extends TransformerAbstract
{
    public function transform(DesignStage $design_stage)
    {
        return $design_stage->info();
    }
}