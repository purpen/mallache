<?php

namespace App\Http\Transformer;

use App\Models\DesignClient;
use League\Fractal\TransformerAbstract;

class DesignClientTransformer extends TransformerAbstract
{
    public function transform(DesignClient $designClient)
    {
        return $designClient->info();
    }
}