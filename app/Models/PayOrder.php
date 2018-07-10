<?php

namespace App\Models;

class PayOrder extends BaseModel
{
    protected $table = 'pay_order';

    protected $fillable = ['uid', 'user_id', 'type', 'item_id', 'summary', 'amount', 'bank_id', 'pay_type', 'item_stage_id','source'];

    protected $appends = ['status_value', 'pay_type_value', 'bank'];

    //一对一相对关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    //支付状态值
    public function getStatusValueAttribute()
    {
        switch ($this->status) {
            case -1:
                $val = '已关闭';
                break;
            case 0:
                $val = '等待支付';
                break;
            case 1:
                $val = '支付完成';
                break;
            case 2:
                $val = '已退款';
                break;
            default:
                $val = '';
        }
        return $val;
    }

    //支付方式； 1.自平台；2.支付宝；3.微信；4：京东；5.银行转账
    public function getPayTypeValueAttribute()
    {
        switch ($this->pay_type) {
            case 1:
                $val = '自平台';
                break;
            case 2:
                $val = '支付宝';
                break;
            case 3:
                $val = '微信支付';
                break;
            case 4:
                $val = '京东支付';
                break;
            case 5:
                $val = '公对公打款';
                break;
            default:
                $val = '';
        }
        return $val;
    }

    public function getBankAttribute()
    {
        $bank_id = $this->bank_id;
        if (array_key_exists($bank_id, config('constant.bank'))) {
            $bank = config('constant.bank')[$bank_id];
        } else {
            $bank = '';
        }
        return $bank;
    }

}
