<?php

namespace App\Http\Transformer;

use App\Models\User;
use App\Models\PayOrder;
use App\Models\AssetModel;
use App\Models\ResultEvaluate ;
use League\Fractal\TransformerAbstract;

class MyOrderListTransformer extends TransformerAbstract
{
    public function transform(PayOrder $pay_order)
    {
        if($pay_order->design_user_id > 0){
            $user = User::find($pay_order->design_user_id);
            $pay_order->demand_company_name = $user->demandCompany->company_name ?? '';
        }
        $pay_order->design_result = $pay_order->designResult;
        $pay_order->company_name = $pay_order->design_result->designCompany->company_name ?? '';
        $cover = AssetModel::getOneImage($pay_order->design_result->cover_id);
        unset($pay_order->design_result->designCompany);
        $pay_order->design_result->is_evaluate = ResultEvaluate::where('design_result_id',$pay_order->design_result->id)->count();
        return [
            'id' => $pay_order->id,
            'uid' => $pay_order->uid,
            'user_id' => $pay_order->user_id,
            'type' => $pay_order->type,
            'status' => $pay_order->status,
            'summary' => $pay_order->summary,
            'pay_type' => $pay_order->pay_type,
            'pay_no' => $pay_order->pay_no,
            'amount' => $pay_order->amount,
            'bank_transfer' => $pay_order->bank_transfer,
            'source' => $pay_order->source,
            'bank_id' => $pay_order->bank_id,
            'bank_transfer' => $pay_order->bank_transfer,
            'design_result_id' => $pay_order->design_result_id,
            'created_at' => $pay_order->created_at,
            'company_name' => $pay_order->company_name,
            'demand_company_name' => $pay_order->demand_company_name,
            'cover' => $cover,
            'design_result' => $pay_order->design_result ?? '',
        ];
    }
}
