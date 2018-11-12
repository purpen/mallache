<?php

namespace App\Http\AdminTransformer;

use App\Models\DemandCompany;
use League\Fractal\TransformerAbstract;

class AdminDesignResultCollectTransformer extends TransformerAbstract
{
    public function transform(DemandCompany $demandCompany)
    {
        return [
            'company_name' => $demandCompany->company_name,
            'contact_name' => $demandCompany->contact_name,
            'company_abbreviation' => $demandCompany->company_abbreviation,
            'address' => $demandCompany->address,
            'phone' => $demandCompany->phone,
        ];
    }
}