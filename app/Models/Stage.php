<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends BaseModel
{
    protected $table = 'stages';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'item_id',
        'title',
    ];
}
