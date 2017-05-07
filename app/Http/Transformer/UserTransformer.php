<?php

namespace App\Http\Transformer;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /*id	int(10)	否		用户ID
account	varchar(20)	否		用户名
password	varchar(50)	否		密码
username	varchar(50)	是		昵称
email	varchar(50)	是		邮箱
phone	varchar(20)	否		手机号码
type	tinyint(4)	否	0	类型：1.需求公司；2.设计公司；
status	tinyint(1)	否	0	状态：；-1：禁用；0.激活;2...
item_sum	int(10)	否	0	项目数量
price_total	decimal(10,2)	否	0	总金额
price_frozen	decimal(10,2)	否	0	冻结金额*/
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
            'price_total' => floatValue($user->price_total),
            'price_frozen' => floatValue($user->price_frozen),
            'img' => $user->image,
            'design_company_id' =>$user->design_company_id,
        ];
    }
}