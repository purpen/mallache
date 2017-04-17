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
    protected $table = 'quotation';


    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['user_id' , 'item_demand_id' , 'design_company_id' , 'price' , 'summary' , 'status'];


    /**
     * 报价与设计公司关联
     */
    public function designCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel' , 'design_company_id');
    }

    /**
     * 获取报价与需求公司关联
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item' , 'item_demand_id');
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
