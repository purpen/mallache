<?php

namespace App\Http\Transformer;

use App\Models\AssetModel;
use App\Models\QuotationModel;
use League\Fractal\TransformerAbstract;

class DesignQuotationTransformer extends TransformerAbstract
{
    public function transform(QuotationModel $quotation)
    {
        return $quotation->info();
    }
}