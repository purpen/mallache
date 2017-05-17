<?php

namespace App\Http\Transformer;

use App\Models\FundLog;
use League\Fractal\TransformerAbstract;

class FundLogTransformer extends TransformerAbstract
{
    public function transform(FundLog $fundLog)
    {
        /*
            id	                int(10)	        否
            user_id	            int(10)	        否		用户ID
            type	            tinyint(4)	    否  		交易类型：-1：出账；1.入账
            transaction_type	tinyint(4)	    否		交易对象类型： 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
            target_id	        varcahr(50)	    否	''	交易对象 id或交易单号
            Amount	            decimal(10.2)	否	0	交易金额
            summary	            varchar(500)	是		备注
        */
        return [
            'id' => intval($fundLog->id),
            'user_id' => (int)$fundLog->user_id,
            'type' => (int)$fundLog->type,
            'transaction_type' => (int)$fundLog->transaction_type,
            'target_id' => $fundLog->target_id,
            'amount' => floatval($fundLog->amount),
            'summary' => $fundLog->summary,
        ];
    }
}