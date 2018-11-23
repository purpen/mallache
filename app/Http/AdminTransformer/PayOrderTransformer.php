<?php

namespace App\Http\AdminTransformer;

use App\Models\AssetModel;
use App\Models\PayOrder;
use App\Models\DemandCompany ;
use League\Fractal\TransformerAbstract;

class PayOrderTransformer extends TransformerAbstract
{
    public function transform(PayOrder $payOrder)
    {
        if ($payOrder->item) {
            $name = array_key_exists('name', $payOrder->item->itemInfo()) ? $payOrder->item->itemInfo()['name'] : '';
            $company_name = $payOrder->item->company_name;
        } else {
            if($payOrder->type == 5){
                //成果需求公司名称
                $res = DemandCompany::where('user_id',$payOrder->user_id)->first();
                if($res){
                    $company_name = $res->company_name ?? '';
                }else{
                    $company_name = '';
                }
            }else{
                $company_name = '';
            }
            $name = '';
        }
        $payOrder->user;
        $payOrder->item_name = $name;
        $payOrder->design_result_name = $payOrder->designResult->title ?? '';
        $payOrder->company_name = $company_name;
        $payOrder->assets = AssetModel::getOneImageUrl($payOrder->id, 33); // 转账附件
        unset($payOrder->item);

        return $payOrder->toArray();
    }
}