<?php

namespace App\Http\Transformer;

use App\Models\DesignCompanyModel;
use League\Fractal\TransformerAbstract;

class DesignCompanyStatisticsTransformer extends TransformerAbstract
{
    /*
     *  id	            int(10)	    否		ID
        company_name	varchar(50)	是		公司名称
        company_abbreviation	varchar(50)	是		公司简称
        province_value	    int(10)	    是		省份
        city_value	        int(10)	    是		城市
        address	        varchar(50)	是		详细地址
        contact_name	varchar(20)	是		联系人姓名
        phone	        varchar(20)	是		手机
        design_statistic	        是		设计公司信息
    */

    public function transform(DesignCompanyModel $DesignCompany)
    {
        return [
            //'id' => $DesignCompany->id,
            'company_name' => $DesignCompany->company_name,
            'contact_name' => $DesignCompany->contact_name,
            'company_abbreviation' => $DesignCompany->company_abbreviation,
            'province_value' => $DesignCompany->company_province_value,
            'city_value' => $DesignCompany->company_city_value,
            'address' => $DesignCompany->address,
            'phone' => $DesignCompany->phone,
            'design_statistic' => $DesignCompany->design_statistic
        ];
    }
}