<?php

namespace App\Http\Transformer;

use App\Models\Works;
use League\Fractal\TransformerAbstract;

class WorksOpenTransformer extends TransformerAbstract
{
    /*
        id	            int(10)	        否		ID
        user_id	        int(10)	        否		用户ID
        company_id      int(10)         否    公司ID
        match_id	        int(10)	        否		所属大赛ID
        title	        varchar(50)	    否		标题
        content	        text(4)	    是		作品描述
        summary	        varchar	    是		备注
        view_count	    int(4)  	  是		浏览量
        tags            varchar     是    标签
        cover_id        int(11)     否    封面ID
        type	          tinyint(4)	    否		类型：1.默认；2.--；
        published	    tinyint(4)	    是		是否发布：0.否；1.是；
        status	    tinyint(4)	    是		状态：0.禁用；1.启用；
    */

    public function transform(Works $works)
    {
        return [
            'id' => intval($works->id),
            'title' => strval($works->title),
            'content' => $works->content,
            'summary' => $works->summary,
            'user_id' => intval($works->user_id),
            'company_id' => intval($works->company_id),
            'match_id' => intval($works->match_id),
            'view_count' => intval($works->view_count),
            'images' => $works->images,
            'cover' => $works->cover,
            'cover_id' => $works->cover_id,
            'type' => intval($works->type),
            'published' => intval($works->published),
            'status' => intval($works->status),
            'company' => $this->company($works),
        ];
    }

    protected function company(Works $works)
    {
        $company = $works->company;
        return [
            'id' => intval($company->id),
            'user_id' => intval($company->user_id),
            'company_type' => intval($company->company_type),
            'company_type_val' => $company->company_type_val,
            'company_name' => strval($company->company_name),
            'company_abbreviation' => strval($company->company_abbreviation),
            'registration_number' => strval($company->registration_number),
            'province' => intval($company->province),
            'city' => intval($company->city),
            'area' => intval($company->area),
            'province_value' => $company->company_province_value,
            'city_value' => $company->company_city_value,
            'area_value' => $company->company_area_value,
            'address' => strval($company->address),
            'contact_name' => strval($company->contact_name),
            'position' => strval($company->position),
            'phone' => '',
            'email' => '',
            'company_size' => intval($company->company_size),
            'company_size_val' => $company->company_size_val,
            'branch_office' => intval($company->branch_office),
            'good_field' => $company->good_field,
            'web' => strval($company->web),
            'company_profile' => strval($company->company_profile),
            'design_type_value' => $company->design_type_value,
            'establishment_time' => strval($company->establishment_time),
            'professional_advantage' => strval($company->professional_advantage),
            'awards' => strval($company->awards),
            'score' => intval($company->score),
            'status' => intval($company->status),
            'is_recommend' => intval($company->is_recommend),
            'verify_status' => intval($company->verify_status),
            'logo' => $company->logo,
            'logo_image' => $company->logo_image,
            'license_image' => $company->license_image,
            'unique_id' => strval($company->unique_id),
            'created_at' => $company->created_at,
            'city_arr' => $company->city_arr,
//            'legal_person' => strval($company->legal_person),
//            'document_type' => intval($company->document_type),
//            'document_type_val' => $company->document_type_val,
//            'document_number' => strval($company->document_number),
//            'document_image' => $company->document_image,
//            'open' => $company->open,
        ];
    }
}
