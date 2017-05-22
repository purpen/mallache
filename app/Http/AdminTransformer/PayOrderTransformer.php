<?php
namespace App\Http\AdminTransformer;

use App\Models\PayOrder;
use League\Fractal\TransformerAbstract;

class PayOrderTransformer extends TransformerAbstract
{
    public function transform(PayOrder $payOrder)
    {
        if($payOrder->item){
            $name = array_key_exists('name', $payOrder->item->itemInfo()) ? $payOrder->item->itemInfo()->name : '';
            $company_name = $payOrder->item->company_name;
        }else{
            $name = '';
            $company_name = '';
        }
        $payOrder->user;
        $payOrder->item_name = $name;
        $payOrder->company_name =  $company_name;
        unset($payOrder->item);
        return $payOrder;
    }
}