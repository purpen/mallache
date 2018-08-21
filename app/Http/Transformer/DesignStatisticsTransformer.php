<?php
namespace App\Http\Transformer;

use App\Models\DesignStatistics;
use League\Fractal\TransformerAbstract;

class DesignStatisticsTransformer extends TransformerAbstract
{
    public function transform(DesignStatistics $designStatistics)
    {
        return [
            'id' => $designStatistics->id,
            'design_company_id' => $designStatistics->design_company_id,
            'score' => $designStatistics->score,
            'jump_count' => $designStatistics->jump_count,
            'level' => $designStatistics->level,
            'average_price' => $designStatistics->average_price,
            'last_time' => !empty($designStatistics->last_time) ? date('Y-m-d H:i',$designStatistics->last_time) : '',
            'recommend_count' => $designStatistics->recommend_count,
            'cooperation_count' => $designStatistics->cooperation_count,
            'success_rate' => $designStatistics->success_rate,
            'case' => $designStatistics->case,
            //'created_at' => !empty($designStatistics->created_at) ? date('Y-m-d H:i',$designStatistics->created_at) : '',
            //'updated_at' => !empty($designStatistics->updated_at) ? date('Y-m-d H:i',$designStatistics->updated_at) : '',
            'intervene' => $designStatistics->intervene,
            'recommend_time' => !empty($designStatistics->recommend_time) ? date('Y-m-d H:i',$designStatistics->recommend_time) : '',
            'company_name' => isset($designStatistics->company_name) ? $designStatistics->company_name : '',
        ];
    }
}