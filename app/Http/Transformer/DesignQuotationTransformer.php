<?php

namespace App\Http\Transformer;

use App\Models\AssetModel;
use App\Models\QuotationModel;
use League\Fractal\TransformerAbstract;

class DesignQuotationTransformer extends TransformerAbstract
{
    public function transform(QuotationModel $quotation)
    {
        return [
            'id' => $quotation->id,
            'company_name' => $quotation->designProject->company_name,
            'contact_name' => $quotation->designProject->contact_name,
            'phone' => $quotation->designProject->phone,
            'position' => $quotation->designProject->position,
            'province' => $quotation->designProject->province,
            'province_value' => $quotation->designProject->province_value,
            'city' => $quotation->designProject->city,
            'city_value' => $quotation->designProject->city_value,
            'area' => $quotation->designProject->area,
            'area_value' => $quotation->designProject->area_value,
            'address' => $quotation->designProject->address,

            'design_company_name' => $quotation->designProject->design_company_name,
            'design_contact_name' => $quotation->designProject->design_contact_name,
            'design_position' => $quotation->designProject->design_position,
            'design_phone' => $quotation->designProject->design_phone,
            'design_province' => $quotation->designProject->design_province,
            'design_province_value' => $quotation->designProject->design_province_value,
            'design_city' => $quotation->designProject->design_city,
            'design_city_value' => $quotation->designProject->design_city_value,
            'design_area' => $quotation->designProject->design_area,
            'design_area_value' => $quotation->designProject->design_area_value,
            'design_address' => $quotation->designProject->design_address,

            'project_name' => $quotation->designProject->name,
            'summary' => $quotation->summary,
            'plan' => $quotation->getProjectPlan(),
            'is_tax' => intval($quotation->is_tax),
            'is_invoice' => intval($quotation->is_invoice),
            'tax_rate' => intval($quotation->tax_rate),
            'total_price' => floatval($quotation->total_price),
            'price' => floatval($quotation->price),
            'asset' => AssetModel::getImageUrl($quotation->id, 30),
        ];
    }
}