<?php

namespace App\Http\Transformer;

use App\Models\Bank;
use League\Fractal\TransformerAbstract;

class BankTransformer extends TransformerAbstract
{
    /**
        id	            int(10)	        否
        user_id	        int(10)	        否		用户ID
        account_name	varchar(30)	    否		开户名
        bank_id	        tinyint(4)	    否		开户行
        branch_name	    varchar(30)	    否		支行名称
        account_number	varchar(50)	    否		银行账号
        province	    int(10)	        否		省份
        city	        int(10)	        否		城市
        status	        tinyint(4)	    否	0	状态: -1.删除；0.正常
        summary	        varchar(200)	是	“”	备注
     */
    public function transform(Bank $bank)
    {
        return [
            'id' => intval($bank->id),
            'user_id' => intval($bank->user_id),
            'account_name' => strval($bank->account_name),
            'bank_id' => intval($bank->bank_id),
            'branch_name' => strval($bank->branch_name),
            'account_number' => strval($bank->account_number),
            'province' => intval($bank->province),
            'city' => intval($bank->city),
            'status' => intval($bank->status),
            'summary' => strval($bank->summary),
            'bank_val' => $bank->bank_val,
            'bank_province_value' => $bank->bank_province_value,
            'bank_city_value' => $bank->bank_city_value,
            'default' => $bank->default,
        ];
    }

}