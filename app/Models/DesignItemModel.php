<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignItemModel extends Model
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'design_items';

    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['user_id' , 'good_field' , 'project_cycle' , 'min_price' , 'max_price'];

}
