<?php

namespace App\Http\Transformer;

use App\Models\DesignDemand;
use League\Fractal\TransformerAbstract;

class DesignDemandTransformer extends TransformerAbstract
{
    public function transform(DesignDemand $design_demand)
    {

        return $design_demand->demandInfo();

    }
}