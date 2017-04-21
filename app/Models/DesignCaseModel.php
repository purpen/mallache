<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignCaseModel extends Model
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'design_case';

    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'prize',
        'prize_time',
        'mass_production',
        'sales_volume',
        'customer',
        'field',
        'profile',
        'status'
    ];

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    /**
     * 案例图片
     */
    public function getCaseImageAttribute()
    {
        return AssetModel::getImageUrl($this->id , 5 , 1);
    }

    public function getSalesVolumeValAttribute()
    {
        switch ($this->attributes['sales_volume']){
            case 1:
                $sales_volume_val = '100-500w';
                break;
            case 2:
                $sales_volume_val = '500-1000w';
                break;
            case 3:
                $sales_volume_val = '1000-5000w';
                break;
            case 4:
                $sales_volume_val = '5000-10000w';
                break;
            case 5:
                $sales_volume_val = '10000w以上';
                break;
            default:
                $sales_volume_val = '';
        }
        return $sales_volume_val;
    }
}
