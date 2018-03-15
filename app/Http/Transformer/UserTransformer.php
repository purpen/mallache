<?php

namespace App\Http\Transformer;

use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * 用户信息 （用户自己使用）
 * Class UserTransformer
 * @package App\Http\Transformer
 */
class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => (int)$user->id,
            'type' => (int)$user->type,
            'account' => $user->account,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status,
            'item_sum' => $user->item_sum,
            'price_total' => $user->price_total,
            'price_frozen' => $user->price_frozen,
            'cash' => $user->cash,
            'logo_image' => $user->logo_image,
            'design_company_id' =>$user->design_company_id,
            'role_id' => $user->role_id,
            'demand_company_id' => $user->demand_company_id,
            'realname' => $user->realname,
            'child_account' => $user->realname,
            'company_role' => $user->realname,
            'design_company_name' => $user->designCompany ? $user->designCompany->company_name : '',
            'design_company_abbreviation' => $user->designCompany ? $user->designCompany->company_abbreviation : '',

        ];
    }
}