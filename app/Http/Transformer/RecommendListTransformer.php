<?php
/**
 * 推荐公司列表转换
 */
namespace App\Http\Transformer;

use App\Models\DesignCompanyModel;
use League\Fractal\TransformerAbstract;

class RecommendListTransformer extends TransformerAbstract
{
    public function transform(DesignCompanyModel $design_company)
    {
        return [
            'id' => intval($design_company->id),
            'user_id' => intval($design_company->user_id),
            'company_type' => intval($design_company->company_type),
            'company_type_val' => $design_company->company_type_val,
            'company_name' => strval($design_company->company_name),
            'company_abbreviation' => strval($design_company->company_abbreviation),
            'registration_number' => strval($design_company->registration_number),
            'province' => intval($design_company->province),
            'city' => intval($design_company->city),
            'area' => intval($design_company->area),
            'province_value' => $design_company->company_province_value,
            'city_value' => $design_company->company_city_value,
            'area_value' => $design_company->company_area_value,
            'address' => strval($design_company->address),
            'contact_name' => strval($design_company->contact_name),
            'position' => strval($design_company->position),
            'phone' => strval($design_company->phone),
            'email' => strval($design_company->email),
            'company_size' => intval($design_company->company_size),
            'company_size_val' => $design_company->company_size_val,
            'branch_office' => intval($design_company->branch_office),
            'good_field' => $design_company->good_field,
            'web' => strval($design_company->web),
            'company_profile' => strval($design_company->company_profile),
            'design_type' => strval($design_company->design_type),
            'establishment_time' => strval($design_company->establishment_time),
            'professional_advantage' => strval($design_company->professional_advantage),
            'awards' => strval($design_company->awards),
            'score' => intval($design_company->score),
            'status' => intval($design_company->status),
            'is_recommend' => intval($design_company->is_recommend),
            'verify_status' => intval($design_company->verify_status),
            'logo' => $design_company->logo,
            'logo_image' => $design_company->logo_image,
            'license_image' => $design_company->license_image,
            'unique_id' => strval($design_company->unique_id),
            'city_arr' => $design_company->city_arr,
        ];
    }

    protected function designCase()
    {

    }
}