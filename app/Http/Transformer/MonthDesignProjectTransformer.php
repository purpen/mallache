<?php

namespace App\Http\Transformer;

use App\Models\DesignProject;
use League\Fractal\TransformerAbstract;

class MonthDesignProjectTransformer extends TransformerAbstract
{
    public function transform(DesignProject $month_item_counts)
    {

        return [
            'id' => (int)$month_item_counts->id,
            'cost' => $month_item_counts->cost,
            'created_at' => $month_item_counts->created_at,
            'total_money' => $month_item_counts->total_money,
            'total_count' => (int)$month_item_counts->total_count,
            'average' => $month_item_counts->average,
        ];
    }
}