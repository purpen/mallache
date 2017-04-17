<?php
/**
 * 推荐公司列表转换
 */
namespace App\Http\Transformer;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class RecommendListTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'design_company' => $user->designCompany ?? '',
            'design_case' =>$user->designCase ?? '',
        ];
    }
}