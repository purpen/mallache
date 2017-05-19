<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationModel extends BaseModel
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'quotation';


    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['user_id' , 'item_demand_id' , 'design_company_id' , 'price' , 'summary' , 'status'];

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 一对一关联 推荐关联表
     */
    public function itemRecommend()
    {
        return $this->hasOne('App\Models\ItemRecommend', 'quotation_id');
    }

    /**
     * 一对一关联 项目表
     */
    public function item()
    {
        return $this->hasOne('App\Models\Item', 'quotation_id');
    }

}
