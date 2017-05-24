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
        'design_type',
        'design_type_paragraph',
        'design_type_contain',
        'total',
        'project_start_date',
        'determine_design_date',
        'structure_layout_date',
        'design_sketch_date',
        'end_date',
        'one_third_total',
        'exterior_design_percentage',
        'exterior_design_money',
        'exterior_design_phase',
        'exterior_modeling_design_percentage',
        'exterior_modeling_design_money',
        'design_work_content',
        'status',
        'unique_id',
        'title',
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
