<?php

namespace App\Http\Transformer;

use App\Models\PayOrder;
use App\Models\AssetModel;
use App\Models\DemandCompany;
use App\Models\ResultEvaluate;
use League\Fractal\TransformerAbstract;

class MyOrderListTransformer extends TransformerAbstract
{
    public function transform(PayOrder $pay_order)
    {
        //需求公司信息
        $demand_company = DemandCompany::query()->where('user_id',$pay_order->user_id)->first();
        if($demand_company){
            $pay_order->demand_company_name = $demand_company->company_name ?? '';
            $pay_order->demand_company_phone = $demand_company->phone ?? '';
        }else{
            $pay_order->demand_company_name = '';
            $pay_order->demand_company_phone = '';
        }
        $pay_order->design_result = $pay_order->designResult;
        //设计公司名称
        $pay_order->company_name = $pay_order->design_result->designCompany->company_name ?? '';
        $pay_order->company_phone = $pay_order->design_result->designCompany->phone ?? '';
        $cover = AssetModel::getOneImage($pay_order->design_result->cover_id);
        unset($pay_order->design_result->designCompany);
        $pay_order->design_result->is_evaluate = ResultEvaluate::where('design_result_id',$pay_order->design_result->id)->count() ? 1 : 0;
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
            'design_user_id' => $pay_order->design_user_id,
            'created_at' => $pay_order->created_at,
            'company_name' => $pay_order->company_name,
            'company_phone' => $pay_order->company_phone,
            'demand_company_name' => $pay_order->demand_company_name,
            'demand_company_phone' => $pay_order->demand_company_phone,
            'cover' => $cover,
            'design_result' => $pay_order->design_result ?? '',
        ];
    }
}
