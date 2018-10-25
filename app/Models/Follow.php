<?php

namespace App\Models;


class Follow extends BaseModel
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'follow';


    /**
     * 获取需求列表信息
     *
     * @param $design_company_id
     * @return array
     */

    static public function showDemandList($design_company_id)
    {
        $data = self::where(['type'=>1,'design_company_id'=>$design_company_id])->get();
        if($data){
            //获取用户
            $arr = [];
            foreach ($data as $v) {
                $arr[] = $v->design_demand_id;
            }
            $designDemand = DesignDemand::whereIn('id',$arr)->get();
            $demand = [];
            foreach ($designDemand as $v) {
                $demand[] = $v->designObtainDemandInfo();
            }
            return $designDemand;
        }
        return $data;
    }

    /**
     * 判断设计公司是否收藏某个设计需求
     *
     * @param $type
     * @param $design_demand_id
     * @param $design_company_id
     * @return bool
     */
    static public function isCollectDemand($design_demand_id, $design_company_id)
    {
        $follow = self::where(['design_demand_id'=>$design_demand_id,'design_company_id'=>$design_company_id])->first();
        if($follow){
            return true;
        }
        return false;
    }


}
