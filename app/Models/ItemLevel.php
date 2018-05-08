<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemLevel extends BaseModel
{
    protected $table = 'item_levels';

    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['user_id' , 'status' , 'name' , 'summary' , 'type'];


}
