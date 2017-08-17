<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends BaseModel
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'contract';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'item_demand_id',
        'design_company_id',
        'demand_company_name',
        'demand_company_address',
        'demand_company_phone',
        'demand_company_legal_person',
        'design_company_name',
        'design_company_address',
        'design_company_phone',
        'design_company_legal_person',
        'item_content',
        'total',
        'design_work_content',
        'status',
        'unique_id',
        'title',
        'warranty_money',
        'first_payment',
        'warranty_money_proportion',
        'first_payment_proportion',
    ];

    //相对关联 项目表
    public function itemDemand()
    {
        return $this->belongsTo('App\Models\Item', 'item_demand_id');
    }

    //相对关联 设计公司表
    public function designCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'design_company_id');
    }


}
