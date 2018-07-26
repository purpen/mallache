<?php

namespace App\Http\Transformer;

use App\Models\DesignTarget;
use League\Fractal\TransformerAbstract;

class DesignTargetTransformer extends TransformerAbstract
{
    /*

    */

    public function transform(DesignTarget $designTarget)
    {
        return [
            'id' => intval($designTarget->id),
            'count' => intval($designTarget->count),
            'design_company_id' => intval($designTarget->design_company_id),
            'turnover' => intval($designTarget->turnover),
            'year' => $designTarget->year ? (int)date('Y',$designTarget->year) : 0,
        ];
    }

}