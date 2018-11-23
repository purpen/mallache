<?php

namespace App\Http\Transformer;

use App\Models\DesignDemand;
use League\Fractal\TransformerAbstract;

class DesignCollectDemandListTransformer extends TransformerAbstract
{
    public function transform(DesignDemand $design_demand)
    {

        return $design_demand->demandListInfo();

    }
}