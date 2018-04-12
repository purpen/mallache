<?php

namespace App\Http\Transformer;

use App\Models\DesignProject;
use League\Fractal\TransformerAbstract;

class DesignProjectTransformer extends TransformerAbstract
{
    public function transform(DesignProject $designProject)
    {
        return $designProject->info();
    }
}