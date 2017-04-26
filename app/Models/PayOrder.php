<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayOrder extends Model
{
    protected $table = 'pay_order';

    protected $fillable = ['uid', 'user_id', 'type','item_id', 'summary'];



}
