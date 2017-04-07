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
    protected $fillable = ['item_demand_id' , 'design_company_id' , 'price' , 'summary' , 'status'];


    /**
     * 获取报价与设计公司关联
     */
    public function designCompany()
    {
        return $this->hasOne('App\Models\DesignCompanyModel');
    }

    /**
     * 获取报价与需求公司关联
     */
    public function demandCompany()
    {
        return $this->hasOne('App\Models\DemandCompanyModel');
    }

}
