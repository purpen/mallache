<?php

namespace App\Models;


class Follow extends BaseModel
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'design_demand';


    /**
     * 设计公司获取需求列表信息
     */
    public function collectListInfo()
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'demand_company_id'=>$this->demand_company_id,
            'design_types'=>$this->design_types,
            'name'=>$this->name,
            'cycle'=>$this->cycle,
            'design_cost'=>$this->design_cost,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }

    /**
     * 获取需求列表信息
     *
     * @param $type
     * @param $design_company_id
     * @return array
     */

    public function showDemandList($type, $design_company_id)
    {
        $data = self::where(['type'=>$type,'design_company_id'=>$design_company_id])->get();
        if($data){
            //获取用户
            $arr = [];
            foreach ($data as $v) {
                $arr[] = $v->design_demand_id;
            }
            $designDemand = DesignDemand::whereIn('id',$arr)->get();
            $demand = [];
            foreach ($designDemand as $v) {
                $demand[] = $v->collectListInfo();
            }
            return $demand;
        }
        return $data;
    }
}
