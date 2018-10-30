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
     * 获取收藏的需求列表信息
     *
     * @author 于海涛
     * @param $design_company_id 设计公司ID
     * @param $per_page 分页
     * @return array
     */
    static public function showDemandList($design_company_id, $per_page)
    {
        $data = self::where(['type'=>1,'design_company_id'=>$design_company_id])->get();
        if($data){
            //获取用户
            $arr = [];
            foreach ($data as $v) {
                $arr[] = $v->design_demand_id;
            }
            $designDemand = DesignDemand::whereIn('id',$arr)->paginate($per_page);
            return $designDemand;
        }
        return $data;
    }

    /**
     * 判断设计公司是否收藏某个设计需求
     *
     * @author 于海涛
     * @param $design_demand_id 设计需求ID
     * @param $design_company_id 设计公司ID
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

    /**
     * 设计成果关注列表
     *
     * @author 王松
     * @param $id 对象id
     * @param $type 类型 1:需求公司,2:设计公司
     * @return array
     */
    public function getResultFollow($id,$type)
    {
        if($type == 1){
            $data = self::where(['design_company_id'=>$id,'type'=>2])->get()
                ->pluck('design_result_id')->all();
        }else{
            $data = self::where(['demand_company_id'=>$id,'type'=>2])->get()
                ->pluck('design_result_id')->all();
        }
        return $data;
    }

    /**
     * 添加收藏更改收藏数量
     *
     * @author 于海涛
     * @param $design_demand_id 设计需求ID
     * @return bool
     */
    public function addCollect($design_demand_id)
    {
        $demand = DesignDemand::where('id',$design_demand_id)->first();
        if($demand){
            if($demand->follow_count < 0){
                $demand->follow_count = 0;
                $demand->save();
            }
            $demand->follow_count = $demand->follow_count+1;
            return $demand->save();
        }

        return $demand;
    }

    /**
     * 取消收藏更改收藏数量
     *
     * @author 于海涛
     * @param $design_demand_id 设计需求ID
     * @return bool
     */
    public function cancelCollect($design_demand_id)
    {
        $demand = DesignDemand::where('id',$design_demand_id)->first();
        if($demand){
            if($demand->follow_count <= 0){
                $demand->follow_count = 0;
                return $demand->save();
            }
            $demand->follow_count = $demand->follow_count-1;
            return $demand->save();
        }

        return $demand;
    }

    /**
     * 后台查看需求被那些设计公司收藏
     *
     * @author 于海涛
     * @param $design_demand_id 设计需求ID
     * @return array
     */
    static public function adminCollectInfo($design_demand_id)
    {
        $designs = self::where(['type'=>1,'design_demand_id'=>$design_demand_id])->get();
        if($designs) {
            $arr = [];
            foreach ($designs as $v) {
                $arr[] = $v->design_company_id;
            }
            $design_info = DesignCompanyModel::query()
                ->join('users','users.id','=','design_company.user_id')
                ->whereIn('design_company.id',$arr)
                ->get();
            $all = [];
            foreach ($design_info as $v) {
                $all[] = $v->designInfo();
            }
            return $all;
        }
        return [];
    }

}
