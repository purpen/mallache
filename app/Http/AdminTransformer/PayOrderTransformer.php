<?php
namespace App\Http\AdminTransformer;

use App\Models\PayOrder;
use League\Fractal\TransformerAbstract;

class PayOrderTransformer extends TransformerAbstract
{
    public function transform(PayOrder $payOrder)
    {
        $payOrder->user;
        return $payOrder;
    }
}