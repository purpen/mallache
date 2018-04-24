<?php

namespace App\Http\Transformer;

use App\Models\DesignStageNode;
use League\Fractal\TransformerAbstract;

class DesignStageNodeTransformer extends TransformerAbstract
{
    public function transform(DesignStageNode $design_stage_node)
    {
        return $design_stage_node->info();
    }
}