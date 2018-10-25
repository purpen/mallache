<?php

namespace App\Http\AdminTransformer;

use App\Models\DesignDemand;
use League\Fractal\TransformerAbstract;

class AdminDesignDemandListTransformer extends TransformerAbstract
{
    public function transform(DesignDemand $design_demand)
    {

        return $design_demand->demandListInfo();

    }
}