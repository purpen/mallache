<?php
namespace App\Http\Transformer;

use App\Models\Evaluate;
use League\Fractal\TransformerAbstract;

class EvaluateTransformer extends TransformerAbstract
{
    public function transform(Evaluate $evaluate)
    {
        return $evaluate;
    }
}