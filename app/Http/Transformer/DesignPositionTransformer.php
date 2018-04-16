<?php

namespace App\Http\Transformer;

use App\Models\DesignPosition;
use League\Fractal\TransformerAbstract;

class DesignPositionTransformer extends TransformerAbstract
{
    public function transform(DesignPosition $designPosition)
    {
        return $designPosition->info();
    }
}