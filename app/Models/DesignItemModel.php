<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DesignItemModel extends BaseModel
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'design_item';

    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['user_id', 'good_field', 'project_cycle', 'min_price', 'type', 'design_type'];

    /**
     * 返回字段
     */
    protected $appends = ['type_val', 'design_type_val', 'project_cycle_val'];

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    //判断设计类型
    public function getTypeValAttribute()
    {
        $type = config('constant.type');
        if (array_key_exists($this->type, $type)) {
            return $type[$this->type];
        }

        return '';
    }

    //判断设计类别
    public function getDesignTypeValAttribute()
    {
        $item_type = config('constant.item_type');

        if (array_key_exists($this->type, $item_type)) {
            if (array_key_exists($this->design_type, $item_type[$this->type])) {
                return $item_type[$this->type][$this->design_type];
            }
        }

        return '';
    }

    //判断周期
    public function getProjectCycleValAttribute()
    {
        $item_cycle = config('constant.item_cycle');
        if (!array_key_exists($this->project_cycle, $item_cycle)) {
            return '';
        }

        return $item_cycle[$this->project_cycle];
    }


}
