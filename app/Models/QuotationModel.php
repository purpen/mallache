<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationModel extends Model
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'quotations';


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
}
