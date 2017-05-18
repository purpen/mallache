<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayOrder extends Model
{
    protected $table = 'pay_order';

    protected $fillable = ['uid', 'user_id', 'type','item_id', 'summary', 'amount'];

    //一对一相对关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    //支付状态值
//    public function getStatusValueAttribute()
//    {
//
//    }


}
