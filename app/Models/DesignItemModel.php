<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DesignItemModel extends Model
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
    protected $fillable = ['user_id' , 'good_field' , 'project_cycle' , 'min_price' , 'type' , 'design_type'];

    /**
     * 返回字段
     */
    protected $appends = ['type_val' , 'design_type_val' , 'project_cycle_val'];

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
        return $this->attributes['type'] == 1 ? '产品设计' : 'UI UX 设计';
    }

    //判断设计类别
    public function getDesignTypeValAttribute()
    {
        if($this->attributes['type'] == 1){
            switch ($this->attributes['design_type']){
                case 1:
                    $design_type_val = '产品策略';
                    break;
                case 2:
                    $design_type_val = '产品设计';
                    break;
                case 3:
                    $design_type_val = '结构设计';
                    break;
                default:
                    $design_type_val = '' ;
            }
        }else{
            switch ($this->attributes['design_type']){
                case 1:
                    $design_type_val = 'app设计';
                    break;
                case 2:
                    $design_type_val = '网页设计';
                    break;
                default:
                    $design_type_val = '' ;
            }
        }

        return $design_type_val;
    }

    //判断周期
    public function getProjectCycleValAttribute()
    {
        switch ($this->attributes['project_cycle']){
            case 1:
                $project_cycle_val = '1个月内';
                break;
            case 2:
                $project_cycle_val = '1-2个月';
                break;
            case 3:
                $project_cycle_val = '2个月';
                break;
            case 4:
                $project_cycle_val = '2-4个月';
                break;
            case 5:
                $project_cycle_val = '其他';
                break;
            default:
                $project_cycle_val  = '' ;
        }
        return $project_cycle_val;
    }



}
