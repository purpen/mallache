<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class DesignQuotationController extends BaseController
{
    public function create()
    {
        $rules = [
            'company_name' => 'required|max:100',
            'contact_name' => 'required|max:20',
            'phone' => 'required|max:20',
            'address' => 'required|string|max:200',
            'summary' => 'max:500',
            'is_tax' => 'required|int',
            'is_invoice',
            'tax_rate',
            'total_price',
            'type',
            'design_project_id',
            'item_demand_id'
        ];
    }
}
