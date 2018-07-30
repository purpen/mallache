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
            'total_item_counts' => $designTarget->total_item_counts,
            'item_counts' => $designTarget->item_counts,
            'no_item_counts' => $designTarget->no_item_counts,
            'ok_count_percentage' => $designTarget->ok_count_percentage,
            'no_count_percentage' => $designTarget->no_count_percentage,
            'ok_turnover_percentage' => $designTarget->ok_turnover_percentage,
            'm_money' => $designTarget->m_money,
            'month_on_month' => $designTarget->month_on_month,
            'm_item' => $designTarget->m_item,
            'quarter_on_quarter' => $designTarget->quarter_on_quarter,

        ];
    }

}