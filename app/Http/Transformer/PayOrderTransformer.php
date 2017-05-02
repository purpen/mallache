<?php

namespace App\Http\Transformer;

use App\Models\PayOrder;
use League\Fractal\TransformerAbstract;

class PayOrderTransformer extends TransformerAbstract
{
    public function transform(PayOrder $pay_order)
    {
        /*id	int(10)	否
        uid	varchar(20)	否		支付单号（唯一索引）
        user_id	int(10)	否		用户ID
        type	tinyint(4)	否		支付类型：1.预付押金；2.项目款；
        item_id	int(10)	是	0	目标ID
        status	tinyint(4)	否	0	状态：0.未支付；1.支付成功；
        summary	varcahr(100)	是	‘’	备注
        pay_type	tinyint(4)	是	0	支付方式；1.支付宝；2.微信；3.京东；
        pay_no	varchar(30)	是	’‘	对应平台支付交易号*/
        return [
            'id' => intval($pay_order->id),
            'uid' => $pay_order->uid,
            'user_id' => (int)$pay_order->user_id,
            'type' => (int)$pay_order->type,
            'item_id' => (int)$pay_order->item_id,
            'status' => (int)$pay_order->status,
            'summary' => $pay_order->summary,
            'pay_type' => $pay_order->pay_type,
            'pay_no' => $pay_order->pay_no,
        ];
    }
}