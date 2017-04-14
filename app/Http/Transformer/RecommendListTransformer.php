<?php
/**
 * 推荐公司列表转换
 */
namespace App\Http\Transformer;

use App\Models\DesignCompanyModel;
use League\Fractal\TransformerAbstract;

class RecommendListTransformer extends TransformerAbstract
{
    public function transform(DesignCompanyModel $designCompanyModel)
    {
        return [
            'id' => $designCompanyModel->id,
            'company_name' => $designCompanyModel->company_name,
            'province' => $designCompanyModel->province,
            'city' => $designCompanyModel->city,
            'area' => $designCompanyModel->area,
            'score' => $designCompanyModel->score,
            'design_type' => explode(',', $designCompanyModel->design_type),
            'good_field' => explode(',', $designCompanyModel->good_field),
            'professional_advantage' => $designCompanyModel->professional_advantage,
        ];
    }
}