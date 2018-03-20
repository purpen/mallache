<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUser extends BaseModel
{
    protected $table = 'item_users';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['user_id', 'item_id' , 'status' , 'level' , 'is_creator' , 'type'];


}
