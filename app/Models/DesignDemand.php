<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignDemand extends BaseModel
{
    use SoftDeletes;

    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'design_demand';

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 相对关联到User用户表
     */
    public function demandCompany()
    {
        return $this->belongsTo('App\Models\DemandCompany', 'design_company_id');
    }
    /**
     * 需求列表信息
     */
    public function demandListInfo()
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'demand_company_id'=>$this->demand_company_id,
            'status'=>$this->status,
            'type'=>$this->type,
            'design_types'=>$this->design_types,
            'name'=>$this->name,
            'cycle'=>$this->cycle,
            'design_cost'=>$this->design_cost,
            "follow_count"=>$this->follow_count,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }

    /**
     * 设计公司获取需求列表信息
     */
    public function designObtainDemandInfo()
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
     * 获取需求列表
     * @param $user_id
     * @param $demand_company_id
     * @return array
     */
    static public function getDemandList($user_id, $demand_company_id)
    {
        $design_demand = self::query()
            ->where(['user_id'=>$user_id, 'demand_company_id'=>$demand_company_id])
            ->get();
        if($design_demand){
            $all = [];
            foreach ($design_demand as $v) {
                $all[] = $v->demandListInfo();
            }
            return $all;
        }
        return $design_demand;
    }

    /**
     * 设计公司获取需求列表
     *
     * @return array
     */
    static public function getDesignObtainDemand()
    {
        $design_demand = self::where('status', 2)->get();
        if($design_demand){
            $all = [];
            foreach ($design_demand as $v) {
                $all[] = $v->designObtainDemandInfo();
            }
            return $all;
        }
        return $design_demand;
    }
}
