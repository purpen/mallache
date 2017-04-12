<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandCompany extends Model
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'demand_company';


    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'company_size',
        'company_web',
        'province',
        'city',
        'area',
        'address',
        'contact_name',
        'phone',
        'email'
    ];

}
