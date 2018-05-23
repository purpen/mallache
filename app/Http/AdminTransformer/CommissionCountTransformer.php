<?php

namespace App\Http\AdminTransformer;

use App\Models\CommissionCount;
use League\Fractal\TransformerAbstract;

class CommissionCountTransformer extends TransformerAbstract
{
    public function transform(CommissionCount $commission_count)
    {
        return $commission_count->info();
    }
}