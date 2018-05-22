<?php

namespace App\Http\AdminTransformer;

use App\Models\ItemCommission;
use League\Fractal\TransformerAbstract;

class ItemCommissionTransformer extends TransformerAbstract
{
    public function transform(ItemCommission $item_commission)
    {
        return $item_commission->info();
    }
}