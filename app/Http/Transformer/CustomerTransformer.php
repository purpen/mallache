<?php

namespace App\Http\Transformer;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    /*
        id	            int(10)	否		用户ID
        company_name	varchar(50)	否		公司名称
        province	    int(11)	否		省份
        city	        int(11)	否		城市
        area	        int(11)	否		地区
        address	        varchar(50)	否		详细地址
        contact_name	varchar(20)	否		联系人姓名
        phone	        varcahr(20)	否		手机
        position	    varchar(20)	否	‘’	职位
        design_company_id	int(11)	否		设计公司id
        status	        tinyint(1)	是	0	状态
        sort	        tinyint(1)	是	0	排序
        summary	        varcahr(100)	是		备注
    */

    public function transform(Customer $customer)
    {
        return [
            'id' => intval($customer->id),
            'company_name' => $customer->company_name,
            'address' => $customer->address,
            'contact_name' => $customer->contact_name,
            'position' => $customer->position,
            'phone' => $customer->phone,
            'summary' => $customer->summary,
            'design_company_id' => intval($customer->design_company_id),
            'province' => intval($customer->province),
            'city' => intval($customer->city),
            'area' => intval($customer->area),
            'status' => intval($customer->status),
            'sort' => intval($customer->sort),
            'user_id' => intval($customer->user_id),
            'created_at' => $customer->created_at,
        ];
    }
}
