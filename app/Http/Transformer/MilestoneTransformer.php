<?php

namespace App\Http\Transformer;

use App\Models\Milestone;
use League\Fractal\TransformerAbstract;

class MilestoneTransformer extends TransformerAbstract
{
    public function transform(Milestone $milestone)
    {
        return $milestone->info();
    }
}