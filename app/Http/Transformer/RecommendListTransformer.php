<?php
/**
 * 推荐公司列表转换
 */
namespace App\Http\Transformer;

use App\Models\DesignCompanyModel;
use League\Fractal\TransformerAbstract;

class RecommendListTransformer extends TransformerAbstract
{
    public function transform(DesignCompanyModel $design_company)
    {
        return [
            'design_company' => $design_company,
        ];
    }
}