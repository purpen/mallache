<?php

namespace App\Http\Transformer;

use App\Models\PayOrder;
use App\Models\AssetModel;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

class PayDesignResultTransformer extends TransformerAbstract
{
    public function transform(PayOrder $pay_order)
    {
        /*id	int(10)	否
        uid	varchar(20)	否		支付单号（唯一索引）
        user_id	int(10)	否		用户ID
        type	tinyint(4)	否		支付类型：1.预付押金；2.项目款；
        design_result_id	int(10)	是	0	设计成果ID
        status	tinyint(4)	否	0	状态：0.未支付；1.支付成功；
        summary	varcahr(100)	是	‘’	备注
        pay_type	tinyint(4)	是	0	支付方式；1.支付宝；2.微信；3.京东；
        pay_no	varchar(30)	是	’‘	对应平台支付交易号*/
        return [
            'id' => intval($pay_order->id),
            'uid' => $pay_order->uid,
            'user_id' => (int)$pay_order->user_id,
            'type' => (int)$pay_order->type,
            'status' => (int)$pay_order->status,
            'status_value' => $pay_order->status_value,
            'summary' => $pay_order->summary,
            'pay_type' => $pay_order->pay_type,
            'pay_type_value' => $pay_order->pay_type_value,
            'pay_no' => $pay_order->pay_no,
            'amount' => $pay_order->amount,
            'total_price' => $pay_order->total_price ?? null,
            'bank_id' => $pay_order->bank_id,
            'bank' => $pay_order->bank,
            'design_result' => $pay_order->designResult,
            'created_at' => $pay_order->created_at,
            'design_result_id' => $pay_order->design_result_id,
            'design_user_id' => $pay_order->design_user_id,
            'bank_transfer' => $pay_order->bank_transfer,
            'assets' => AssetModel::getOneImageUrl($pay_order->id, 33), // 转账附件
            'source' => $pay_order->source,
        ];
    }
}