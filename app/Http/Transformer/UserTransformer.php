<?php

namespace App\Http\Transformer;

use App\Models\DemandCompany;
use App\Models\DesignCompanyModel;
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
        $demand = null;
        $design = null;
        if ($user->type == 1) {
            $demand = $user->demandCompany;
        } else {
            $design = $user->designCompany;
        }

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
            'design_company_id' => $user->design_company_id,
            'role_id' => $user->role_id,
            'demand_company_id' => $user->demand_company_id,
            'realname' => $user->realname,
            'position' => $user->position,
            'child_account' => $user->child_account,
            'company_role' => $user->company_role,
            'invite_user_id' => $user->invite_user_id,
            'design_company_name' => $design ? $design->company_name : '',
            'design_company_abbreviation' => $design ? $design->company_abbreviation : '',
            'verify_status' => $design ? $design->verify_status : -1,

            'demand_company_name' => $demand ? $demand->company_name : '',
            'demand_company_abbreviation' => $demand ? $demand->company_abbreviation : '',
            'demand_verify_status' => $demand ? $demand->verify_status : -1,
            'created_at' => $user->created_at,


        ];
    }

}