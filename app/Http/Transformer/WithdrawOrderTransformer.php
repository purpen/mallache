<?php
namespace App\Http\Transformer;

use App\Models\WithdrawOrder;
use League\Fractal\TransformerAbstract;

class WithdrawOrderTransformer extends TransformerAbstract
{
    public function transform(WithdrawOrder $withdrawOrder)
    {
        return [
            "id" => $withdrawOrder->id,
            "uid" => $withdrawOrder->uid,
            "type" => $withdrawOrder->type,
            'amount' => $withdrawOrder->amount,
            "status" => $withdrawOrder->status,
            "account_bank_id" => $withdrawOrder->account_bank_id,
            "account_bank_value" => $withdrawOrder->account_bank_value,
            "account_name" => $withdrawOrder->account_name,
            "account_number" => $withdrawOrder->account_number,
            "branch_name" => $withdrawOrder->branch_name,
            "summary" => $withdrawOrder->branch_name,
            "true_time" => $withdrawOrder->true_time,
            "created_at" => $withdrawOrder->created_at,
            "updated_at" => $withdrawOrder->updated_at,
            "user_id" => $withdrawOrder->user_id,
        ];
    }
}