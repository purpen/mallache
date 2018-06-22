<?php

namespace App\Http\AdminTransformer;

use App\Models\Invoice;
use League\Fractal\TransformerAbstract;

class InvoiceTransformer extends TransformerAbstract
{
    public function transform(Invoice $invoice)
    {
        return $invoice->info();
    }
}