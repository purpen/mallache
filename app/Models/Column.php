<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends BaseModel
{
    /**
     * 关联模型到数据表
     * @var string
     */
    protected $table = 'column';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['type','name','content','url','sort','status'];
}
