<?php

namespace App\Models;

use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;

class Bank extends BaseModel
{
    protected $table = 'bank';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'user_id',
        'account_name',
        'account_bank_id',
        'branch_name',
        'account_number',
        'province',
        'city',
        'status',
        'summary',
        'default',
    ];

    /**
     * 返回
     */
    protected $appends = [
        'bank_val',
        'bank_province_value',
        'bank_city_value',
    ];

    //银行名称
    public function getBankValAttribute()
    {
        $key = $this->attributes['account_bank_id'];
        if(array_key_exists($key,config('constant.bank'))){
            $bank_val = config('constant.bank')[$key];
            return $bank_val;

        }
        return '';
    }

    /**
     * 省份访问修改器
     * @return mixed|string
     */
    public function getBankProvinceValueAttribute()
    {
        return Tools::cityName($this->province);
    }

    /**
     * 城市访问修改器
     * @return mixed|string
     */
    public function getBankCityValueAttribute()
    {
        return Tools::cityName($this->city);
    }

    /**
     * 更改银行信息状态
     */
    static public function status($id, $status= -1)
    {
        $bank = self::findOrFail($id);
        $bank->status = $status;
        return $bank->save();
    }
}
