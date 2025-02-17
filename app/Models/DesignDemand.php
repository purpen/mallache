<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Tools;

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
     * 相对关联到DemandCompany需求公司表
     */
    public function demandCompany()
    {
        return $this->belongsTo('App\Models\DemandCompany', 'demand_company_id');
    }


    /**
     * 后台查看需求信息
     */
    public function adminDemandInfo()
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'account'=>$this->User ? $this->User->account : null,
            'realname'=>$this->User ? $this->User->realname : null,
            'phone'=>$this->User ? $this->User->phone : null,
            'demand_company_id'=>$this->demand_company_id,
            'logo' => $this->demandCompany ? $this->demandCompany->logo : null,
            'logo_image' => $this->demandCompany ? $this->demandCompany->logo_image : null,
            'demand_company_name' => $this->demandCompany ? $this->demandCompany->company_name : null,
            'status'=>$this->status,
            'type'=>$this->type,
            'type_value' => $this->type_value,
            'design_types'=>$this->design_types,
            'design_types_value' => $this->design_types_value,
            'name'=>$this->name,
            'cycle'=>$this->cycle,
            'cycle_value' => $this->cycle_value,
            'design_cost'=>$this->design_cost,
            'design_cost_value' => $this->design_cost_value,
            'industry'=>$this->industry,
            'industry_value'=>$this->industry_value,
            'field'=>$this->field,
            'field_value'=>$this->field_value,
            "follow_count"=>$this->follow_count,
            'item_province'=>$this->item_province,
            'item_province_value'=>$this->province_value,
            'item_city'=>$this->item_city,
            'item_city_value'=>$this->city_value,
            'content'=>$this->content,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }

    /**
     * 需求信息
     */
    public function demandInfo()
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'phone'=>$this->User ? $this->User->phone : null,
            'demand_company_id'=>$this->demand_company_id,
            'logo' => $this->demandCompany ? $this->demandCompany->logo : null,
            'logo_image' => $this->demandCompany ? $this->demandCompany->logo_image : null,
            'demand_company_name' => $this->demandCompany ? $this->demandCompany->company_name : null,
            'status'=>$this->status,
            'type'=>$this->type,
            'type_value' => $this->type_value,
            'design_types'=>$this->design_types,
            'design_types_value' => $this->design_types_value,
            'name'=>$this->name,
            'cycle'=>$this->cycle,
            'cycle_value' => $this->cycle_value,
            'design_cost'=>$this->design_cost,
            'design_cost_value' => $this->design_cost_value,
            'industry'=>$this->industry,
            'industry_value'=>$this->industry_value,
            'field'=>$this->field,
            'field_value'=>$this->field_value,
            "follow_count"=>$this->follow_count,
            "follow_status"=>$this->follow_status,
            'item_province'=>$this->item_province,
            'item_province_value'=>$this->province_value,
            'item_city'=>$this->item_city,
            'item_city_value'=>$this->city_value,
            'content'=>$this->content,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
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
            'company_name'=>$this->demandCompany ? $this->demandCompany->company_name : null,
            'logo'=>$this->demandCompany ? $this->demandCompany->logo : null,
            'logo_image'=>$this->demandCompany ? $this->demandCompany->logo_image : null,
            'phone'=>$this->User ? $this->User->phone : null,
            'status'=>$this->status,
            'type'=>$this->type,
            'type_value' => $this->type_value,
            'design_types'=>$this->design_types,
            'design_types_value' => $this->design_types_value,
            'name'=>$this->name,
            'cycle'=>$this->cycle,
            'cycle_value' => $this->cycle_value,
            'design_cost'=>$this->design_cost,
            'design_cost_value' => $this->design_cost_value,
            "follow_count"=>$this->follow_count,
            "follow_status"=>$this->follow_status,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }


    /**
     * 获取需求列表
     *
     * @author 于海涛
     * @param $user_id 用户ID
     * @param $demand_company_id 需求公司ID
     * @param $per_page 分页
     * @return array
     */
    static public function getDemandList($user_id, $demand_company_id, $per_page)
    {
        $design_demand = self::query()
            ->where(['user_id'=>$user_id, 'demand_company_id'=>$demand_company_id])
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
        return $design_demand;
    }



    /**
     * 获取设计方收藏的需求ID
     *
     * @author 于海涛
     * @param $design_company_id 设计公司ID
     * @return array
     */
    static public function getCollectDemandId($design_company_id)
    {
        $demand_id = Follow::where(['type'=>1,'design_company_id'=>$design_company_id])->get();
        if(!$demand_id->isEmpty()){
            $arr = [];
            foreach ($demand_id as $v) {
                $arr[] = $v->design_demand_id;
            }
            return $arr;
        }
        return [];
    }

    /**
     * 获取图片logo
     *
     * @return array
     */
    public function getLogoImageAttribute()
    {
        return AssetModel::getOneImage($this->logo);
    }

    //设计类型
    public function getTypeValueAttribute()
    {
        $type = config('constant.type');
        if (array_key_exists($this->type, $type)) {
            return $type[$this->type];
        }

        return '';
    }

    //设计类别多选
    public function getDesignTypesValueAttribute()
    {
        $item_type = config('constant.item_type');

        $design_types = json_decode($this->design_types, true);
        if (empty($design_types)) {
            return [];
        } else {
            $arr = [];
            if (array_key_exists($this->type, $item_type)) {
                foreach ($design_types as $v) {
                    if (array_key_exists($v, $item_type[$this->type])) {
                        $arr[] = $item_type[$this->type][$v];
                    }
                }
            }
            return $arr;
        }

    }

    //所属行业
    public function getFieldValueAttribute()
    {
        $field = config('constant.field');
        if (!array_key_exists($this->field, $field)) {
            return '';
        }
        return $field[$this->field];
    }

    //所属行业
    public function getIndustryValueAttribute()
    {
        $industries = config('constant.industry');
        if (!array_key_exists($this->industry, $industries)) {
            return '';
        }
        return $industries[$this->industry];
    }

    //设计周期
    public function getCycleValueAttribute()
    {
        $item_cycle = config('constant.item_cycle');
        if (!array_key_exists($this->cycle, $item_cycle)) {
            return '';
        }

        return $item_cycle[$this->cycle];
    }

    //设计费用
    public function getDesignCostValueAttribute()
    {
        //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
        switch ($this->design_cost) {
            case 1:
                $design_cost_value = '1-5万之间';
                break;
            case 2:
                $design_cost_value = '5-10万之间';
                break;
            case 3:
                $design_cost_value = '10-20万之间';
                break;
            case 4:
                $design_cost_value = '20-30万之间';
                break;
            case 5:
                $design_cost_value = '30-50万之间';
                break;
            case 6:
                $design_cost_value = '50万以上';
                break;
            default:
                $design_cost_value = '';
        }
        return $design_cost_value;
    }

    //省份访问修改器
    public function getProvinceValueAttribute()
    {
        return Tools::cityName($this->item_province) ?? "";
    }

    //城市访问修改器
    public function getCityValueAttribute()
    {
        return Tools::cityName($this->item_city) ?? "";
    }
}
