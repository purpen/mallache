<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundLog extends BaseModel
{
    public $table = 'fund_log';

    protected $fillable = ['user_id', 'amount', 'transaction_type', 'target_id', 'type', 'summary'];

    /**
     * 交易入账流水记录
     *
     * @param int $user_id 用户ID
     * @param float $amount 金额
     * @param int $transaction_type 交易平台 1.平台用户；2.支付宝；3.微信；4：京东；5.银行转账；
     * @param string $target_id 交易对象 (用户id或交易单号）
     */
    public function inFund(int $user_id, float $amount, int $transaction_type, string $target_id, string $summary='')
    {
        self::create([
            'user_id' => $user_id,
            'amount' => $amount,
            'transaction_type' => $transaction_type,
            'target_id' => $target_id,
            'type' => 1,
            'summary' => $summary,
        ]);
    }

    /**
     * 交易出账流水记录
     *
     * @param int $user_id 用户ID
     * @param float $amount 金额
     * @param int $transaction_type 交易平台 1.平台用户；2.支付宝；3.微信；4：京东；5.银行转账；
     * @param string $target_id 交易对象 (用户id或交易单号）
     */
    public function outFund(int $user_id, float $amount, int $transaction_type, string $target_id, string $summary='')
    {
        self::create([
            'user_id' => $user_id,
            'amount' => $amount,
            'transaction_type' => $transaction_type,
            'target_id' => $target_id,
            'type' => -1,
            'summary' => $summary,
        ]);
    }


}
