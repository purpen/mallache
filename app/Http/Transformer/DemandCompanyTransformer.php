<?php

namespace App\Http\Transformer;

use App\Helper\Tools;
use App\Models\DemandCompany;
use League\Fractal\TransformerAbstract;

class DemandCompanyTransformer extends TransformerAbstract
{
    /*id	int(10)	否		ID
    user_id	int(10)	否		用户表ID
    company_name	varchar(50)	否		公司名称
    company_size	tinyint(4)	否		公司规模：1.初创；...
    company_web	varchar(50)	否		企业网址
    province	int(10)	否		省份
    city	int(10)	否		城市
    area	int(10)	否		地区
    address	varchar(50)	否		详细地址
    contact_name	varchar(20)	否		联系人姓名
    phone	varcahr(20)	否		手机
    email	varchar(50)	否		邮箱
    verify_status	tinyint(4)	否	0	审核状态：0.审核中；1.审核通过；
    */

    public function transform(DemandCompany $demand)
    {
        return [
            'id' => intval($demand->id),
            'company_name' => strval($demand->company_name),
            'company_abbreviation' => strval($demand->company_abbreviation),
            'company_size' => intval($demand->company_size),
            'company_size_value' => $demand->company_size_value,
            'company_web' => strval($demand->company_web),
            'province' => intval($demand->province),
            'city' => intval($demand->city),
            'area' => intval($demand->area),
            'province_value' => $demand->province_value,
            'city_value' => $demand->city_value,
            'area_value' => $demand->area_value,
            'address' => strval($demand->address),
            'contact_name' => strval($demand->contact_name),
            'phone' => $demand->phone,
            'email' => strval($demand->email),
            'image' => $demand->image,
            'verify_status' => $demand->verify_status,
            'license_image' => $demand->license_image,
            'position' =>$demand->position,
            'company_type' => $demand->company_type,
            'company_type_value' => $demand->company_type_value,
            'registration_number' => $demand->registration_number,
            'legal_person' => $demand->legal_person,
            'document_type' => $demand->document_type,
            'document_type_value' => $demand->document_type_value,
            'document_number' => $demand->document_number,
            'company_property' => $demand->company_property,
            'company_property_value' => $demand->company_property_value,
            'document_image' => $demand->document_image,

        ];
    }
}