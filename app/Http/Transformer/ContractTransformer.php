<?php

namespace App\Http\Transformer;

use App\Models\Contract;
use League\Fractal\TransformerAbstract;

class ContractTransformer extends TransformerAbstract
{
    /*
            item_demand_id	                    int(10)	    否		项目ID
            design_company_id	                int(10)	    否		设计公司ID
            demand_company_name	                string(50)	是	空	需求公司名称
            demand_company_address	            string(50)	是	空	需求公司地址
            demand_company_phone	            string(20)	是	空	需求公司电话
            demand_company_legal_person	        string(20)	是	空	需求公司法人
            design_company_name	                string(50)	是	空	设计公司名称
            design_company_address	            string(50)	是	空	设计公司地址
            design_company_phone	            string(20)	是	空	设计公司电话
            design_company_legal_person	        string(20)	是	空	设计公司法人
            design_type	                        string(20)	是	空	设计类型
            design_type_paragraph	            string(20)	是	空	设计类型几款
            design_type_contain	                string(20)	是	空	设计类型包含
            total	                            string(20)	是	空	总额
            project_start_date	                int(10)	    是	0	项目启动日期
            determine_design_date	            int(10)	    是	0	设计确定日期
            structure_layout_date	            int(10)	    是	0	结构布局验证日期
            design_sketch_date	                int(10)	    是	0	效果图日期
            end_date	                        int(10)	    是	0	最后确认日期
            one_third_total	                    string(20)	是	空	30%总额
            exterior_design_percentage	        int(10)	    是	0	外观设计百分比
            exterior_design_money	            string(20)	是	空	外观设计金额
            exterior_design_phase	            string(20)	是	空	外观设计阶段
            exterior_modeling_design_percentage	int(10)	    是	0	外观建模设计百分比
            exterior_modeling_design_money	    string(20)	是	空	外观建模设计金额
            design_work_content	                string(50)	是	空	设计工作内容
            status	                            tinyint(10)	否	0	合同状态.0可以修改.1不可以
            unique_id	                        string(30)	否	0	唯一id

    */

    public function transform(Contract $contract)
    {
        return [
            'id' => intval($contract->id),
            'item_demand_id' => intval($contract->item_demand_id),
            'design_company_id' => intval($contract->design_company_id),
            'demand_company_name' => strval($contract->demand_company_name),
            'demand_company_address' => strval($contract->demand_company_address),
            'demand_company_phone' => strval($contract->demand_company_phone),
            'demand_company_legal_person' => strval($contract->demand_company_legal_person),
            'design_company_name' => strval($contract->design_company_name),
            'design_company_address' => strval($contract->design_company_address),
            'design_company_phone' => strval($contract->design_company_phone),
            'design_company_legal_person' => strval($contract->design_company_legal_person),
            'design_type' => strval($contract->design_type),
            'design_type_paragraph' => strval($contract->design_type_paragraph),
            'design_type_contain' => strval($contract->design_type_contain),
            'total' => strval($contract->total),
            'project_start_date' => intval($contract->project_start_date),
            'determine_design_date' => intval($contract->determine_design_date),
            'structure_layout_date' => intval($contract->structure_layout_date),
            'design_sketch_date' => intval($contract->design_sketch_date),
            'end_date' => intval($contract->end_date),
            'one_third_total' => strval($contract->one_third_total),
            'exterior_design_percentage' => intval($contract->exterior_design_percentage),
            'exterior_design_money' => strval($contract->exterior_design_money),
            'exterior_design_phase' => strval($contract->exterior_design_phase),
            'exterior_modeling_design_percentage' => intval($contract->exterior_modeling_design_percentage),
            'exterior_modeling_design_money' => strval($contract->exterior_modeling_design_money),
            'design_work_content' => strval($contract->design_work_content),
            'status' => intval($contract->status),
            'unique_id' => strval($contract->unique_id),
        ];
    }
}