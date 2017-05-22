<?php

namespace App\Models;

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
      'bank_id',
      'branch_name',
      'account_number',
      'province',
      'city',
      'status',
      'summary',
    ];
}
