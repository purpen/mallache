<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * 关联模型到数据表
     *
     * @var string
     */
    protected $table = 'category';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['type','name','pid','status'];
}
