<?php

namespace App\Http\Transformer;

use App\Models\DesignCompanyModel;
use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * 用户信息 （子账户使用）
 * Class UserTransformer
 * @package App\Http\Transformer
 */
class ChildUserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $design = DesignCompanyModel::where('id' , $user->design_company_id)->first();
        return [
            'id' => (int)$user->id,
            'username' => $user->username,
            'phone' => $user->phone,
            'status' => $user->status,
            'logo_image' => $user->logo_image ? ($user->logo_image)['logo'] : '',
            'realname' => $user->realname,
            'child_account' => $user->child_account,
            'company_role' => $user->company_role,
            'invite_user_id' => $user->invite_user_id,
            'design_company_id' => $user->design_company_id,
            'design_company_name' => $design->company_name,
            'design_company_abbreviation' => $design->company_abbreviation,
            'created_at' => $user->created_at,

        ];
    }
}