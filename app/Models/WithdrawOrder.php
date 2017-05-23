<?php
/**
 * Created by PhpStorm.
 * User: llh
 * Date: 2017/5/22
 * Time: 13:59
 */

namespace App\Models;


class WithdrawOrder extends BaseModel
{
    //对应表名
    protected $table = 'withdraw_order';

    protected $fillable = [
        'uid',
        'user_id',
        'type',
        'amount',
        'account_name',
        'account_number',
        'account_bank_id',
        'branch_name',
        'status',
    ];

    protected $appends = ['account_bank_value'];

    /**
     * 一对多相对关联用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    //银行名称
    public function getAccountBankValueAttribute()
    {
        if(!array_key_exists($this->account_bank_id, config('constant.bank'))){
            return '';
        }

        return config('constant.bank')[$this->account_bank_id];
    }
}