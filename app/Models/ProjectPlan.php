<?php

namespace App\Models;

class ProjectPlan extends BaseModel
{
    protected $table = 'project_plan';

    /**
     * quotation_id    int(10)    否        报价单ID
     * content    varchar(500)    否        工作内容
     * arranged    json    否        人员安排 [{'name':'结构师','number':2}]
     * duration    int(10)    否        持续时间
     * price    decimal(10,2)    否        费用
     * summary    varchar(500)        null    备注
     */
    /**
     * 创建设计报价计划
     * @param array $arr
     * @return bool
     */
    public static function createPlan(array $arr)
    {
        $project_plan = new ProjectPlan();
        $project_plan->quotation_id = $arr['quotation_id'];
        $project_plan->content = $arr['content'];
        $project_plan->arranged = json_encode($arr['arranged']);
        $project_plan->duration = $arr['duration'];
        $project_plan->price = $arr['price'];
        $project_plan->summary = $arr['summary'];
        return $project_plan->save();
    }

}
