<?php

namespace App\Http\AdminTransformer;

use App\Models\AssetModel;
use App\Models\PayOrder;
use League\Fractal\TransformerAbstract;

class PayOrderTransformer extends TransformerAbstract
{
    public function transform(PayOrder $payOrder)
    {
        if ($payOrder->item) {
            $name = array_key_exists('name', $payOrder->item->itemInfo()) ? $payOrder->item->itemInfo()['name'] : '';
            $company_name = $payOrder->item->company_name;
        } else {
            $name = '';
            $company_name = '';
        }
        $payOrder->user;
        $payOrder->item_name = $name;
        $payOrder->company_name = $company_name;
        $payOrder->assets = AssetModel::getOneImageUrl($payOrder->id, 33); // 转账附件
        unset($payOrder->item);

        return $payOrder->toArray();
    }
}