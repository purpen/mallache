<?php

namespace App\Http\Transformer;

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
            'address' => $quotation->designProject->address,

            'design_company_name' => $quotation->designCompany->company_name,
            'design_contact_name' => $quotation->designCompany->contact_name,
            'design_phone' => $quotation->designCompany->phone,
            'design_address' => $quotation->designCompany->address,

            'project_name' => $quotation->designProject->name,
            'summary' => $quotation->summary,
            'plan' => $quotation->getProjectPlan(),
            'is_tax' => $quotation->is_tax,
            'is_invoice' => $quotation->is_invoice,
            'tax_rate' => $quotation->tax_rate,
            'total_price' => $quotation->total_price,
            'price' => $quotation->price,
        ];
    }
}