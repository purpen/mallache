<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class SmallItem extends BaseModel
{
    protected $table = 'small_items';

    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['item_name' , 'user_name' , 'phone' , 'status'];

}
