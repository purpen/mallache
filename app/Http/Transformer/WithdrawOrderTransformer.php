<?php
namespace App\Http\Transformer;

use App\Models\WithdrawOrder;
use League\Fractal\TransformerAbstract;

class WithdrawOrderTransformer extends TransformerAbstract
{
    public function transform(WithdrawOrder $withdrawOrder)
    {
        return $withdrawOrder;
    }
}