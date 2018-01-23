<?php
namespace App\Http\AdminTransformer;

use App\Models\WithdrawOrder;
use League\Fractal\TransformerAbstract;

class WithdrawOrderTransformer extends TransformerAbstract
{
    public function transform(WithdrawOrder $withdrawOrder)
    {
        return $withdrawOrder->toArray();
    }
}