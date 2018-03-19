<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends BaseModel
{
    protected $table = 'customers';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'company_name',
        'address',
        'contact_name',
        'phone',
        'position',
        'summary',
        'status',
        'sort',
        'province',
        'city',
        'area',
        'design_company_id',
    ];
}
