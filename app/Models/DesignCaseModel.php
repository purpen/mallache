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
}
