<?php

namespace App\Http\Transformer;

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
        return [
            'id' => (int)$user->id,
            'username' => $user->username,
            'phone' => $user->phone,
            'status' => $user->status,
            'logo_image' => $user->logo_image,
            'realname' => $user->realname,
            'child_account' => $user->child_account,
            'company_role' => $user->company_role,
            'design_company_name' => $user->designCompany ? $user->designCompany->company_name : '',
            'design_company_abbreviation' => $user->designCompany ? $user->designCompany->company_abbreviation : '',

        ];
    }
}