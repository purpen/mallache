<?php

namespace App\Http\Transformer;

use App\Helper\Tools;
use App\Models\DesignCompanyModel;
use League\Fractal\TransformerAbstract;

class DesignCompanyShowTransformer extends TransformerAbstract
{
    /*
     *  id	            int(10)	    否		ID
        user_id	        int(10)	    是		用户表ID
        company_type	tinyint(1)	是		企业类型：1.普通；2.多证合一；
        company_name	varchar(50)	是		公司名称
        company_abbreviation	varchar(50)	是		公司简称
        registration_number	varchar(15)	是		注册号
        province	    int(10)	    是		省份
        city	        int(10)	    是		城市
        area	        int(10)	    是		地区
        address	        varchar(50)	是		详细地址
        contact_name	varchar(20)	是		联系人姓名
        position	    varchar(20)	是		职位
        phone	        varchar(20)	是		手机
        email	        varchar(50)	是		邮箱
        company_size	tinyint(1)	是		公司规模：1.10以下；2.10-50；3.50-100；4.100以上;
        branch_office	tinyint(1)	是	0	分公司：1.有；2.无；
        item_quantity	tinyint(1)	是	0	服务项目：1.10以下；2.10-50；3.50-100；4.100-200;5.200以上
        good_field	    varchar(50)	是		擅长领域 class_id
        web	            varchar(50)	是		设计公司网站
        company_profile	varchar(500)	是		公司简介
        design_type	varchar(50)	    否		产品设计：1.产品策略；2.产品设计；3.结构设计；ux设计：4.app设计；5.网页设计；
        establishment_time	date	是		公司成立时间
        professional_advantage	varchar(500)	是		专业优势
        awards	                varchar(500)	是		荣誉奖项
        score	                int(10)	否	0	设计公司评分
        status	                tinyint(4)	否	0	设计公司状态：0、关闭；1、审核通过；
        is_recommend	tinyint(4)	否	0	是否推荐：0.否；1.是;
        verify_status	tinyint(4)	否	0	审核状态：0.审核中；1.审核通过；
        unique_id	    varchar(30)	否		唯一ID
    */

    public function transform(DesignCompanyModel $DesignCompany)
    {
        return [
            'id' => intval($DesignCompany->id),
            'user_id' => intval($DesignCompany->user_id),
            'company_type' => intval($DesignCompany->company_type),
            'company_type_val' => $DesignCompany->company_type_val,
            'company_name' => strval($DesignCompany->company_name),
            'company_abbreviation' => strval($DesignCompany->company_abbreviation),
            'registration_number' => strval($DesignCompany->registration_number),
            'province' => intval($DesignCompany->province),
            'city' => intval($DesignCompany->city),
            'area' => intval($DesignCompany->area),
            'province_value' => $DesignCompany->company_province_value,
            'city_value' => $DesignCompany->company_city_value,
            'area_value' => $DesignCompany->company_area_value,
            'address' => strval($DesignCompany->address),
            'contact_name' => strval($DesignCompany->contact_name),
            'position' => strval($DesignCompany->position),
            'phone' => intval($DesignCompany->phone),
            'email' => strval($DesignCompany->email),
            'company_size' => intval($DesignCompany->company_size),
            'company_size_val' => $DesignCompany->company_size_val,
            'branch_office' => intval($DesignCompany->branch_office),
            'good_field' => $DesignCompany->good_field,
            'good_field_value' => $DesignCompany->good_field_value,
            'web' => strval($DesignCompany->web),
            'company_profile' => strval($DesignCompany->company_profile),
            'design_type_value' => $DesignCompany->design_type_value,
            'establishment_time' => strval($DesignCompany->establishment_time),
            'professional_advantage' => strval($DesignCompany->professional_advantage),
            'awards' => strval($DesignCompany->awards),
            'score' => intval($DesignCompany->score),
            'status' => intval($DesignCompany->status),
            'is_recommend' => intval($DesignCompany->is_recommend),
            'verify_status' => intval($DesignCompany->verify_status),
            'logo' => $DesignCompany->logo,
            'logo_image' => $DesignCompany->logo_image,
            'license_image' => $DesignCompany->license_image,
            'unique_id' => strval($DesignCompany->unique_id),
            'city_arr' => $DesignCompany->city_arr,
            'legal_person' => strval($DesignCompany->legal_person),
            'document_type' => intval($DesignCompany->document_type),
            'document_type_val' => $DesignCompany->document_type_val,
            'document_number' => strval($DesignCompany->document_number),
            'document_image' => $DesignCompany->document_image,
            'open' => $DesignCompany->open,
            'verify_summary' => $DesignCompany->verify_summary,
            'company_english' => $DesignCompany->company_english,
            'revenue' => $DesignCompany->revenue,
            'revenue_value' => $DesignCompany->revenue_value,
            'weixin_id' => $DesignCompany->weixin_id,
            'high_tech_enterprises' => json_decode($DesignCompany->high_tech_enterprises),
            'industrial_design_center' => json_decode($DesignCompany->industrial_design_center),
            'investment_product' => $DesignCompany->investment_product,
            'own_brand' => json_decode($DesignCompany->own_brand),
            'ave_score' => intval($DesignCompany->ave_score),
            'base_average' => intval($DesignCompany->base_average),
            'credit_average' => intval($DesignCompany->credit_average),
            'business_average' => intval($DesignCompany->business_average),
            'design_average' => intval($DesignCompany->design_average),
            'effect_average' => intval($DesignCompany->effect_average),
            'innovate_average' => intval($DesignCompany->innovate_average),
            'account_name' => $DesignCompany->account_name,
            'bank_name' => $DesignCompany->bank_name,
            'account_number' => $DesignCompany->account_number,
            'taxable_type' => (int)$DesignCompany->taxable_type,
            'invoice_type' => (int)$DesignCompany->invoice_type,
        ];
    }
}