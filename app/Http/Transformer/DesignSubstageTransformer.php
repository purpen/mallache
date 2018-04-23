<?php

namespace App\Http\Transformer;

use App\Models\DesignSubstage;
use League\Fractal\TransformerAbstract;

class DesignSubstageTransformer extends TransformerAbstract
{
    public function transform(DesignSubstage $design_substage)
    {
        return $design_substage->info();
    }
}