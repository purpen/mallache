<?php

namespace App\Http\Transformer;

use App\Models\Nodes;
use League\Fractal\TransformerAbstract;

class NodesTransformer extends TransformerAbstract
{
    public function transform(Nodes $nodes)
    {
        return $nodes->info();
    }
}