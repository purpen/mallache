<?php

namespace App\Models;

//设计公司项目管理
use App\Helper\Tools;
use Dingo\Api\Auth\Auth;
use Illuminate\Support\Facades\Log;

class DesignProject extends BaseModel
{
    protected $table = 'design_project';

    // 批量赋值黑名单
    protected $guarded = ['user_id', 'project_type', 'status', 'token'];

    // 商务经理相对关联用户表
    public function businessManagerUser()
    {
        return $this->belongsTo('App\Models\User', 'business_manager');
    }

    // 项目经理相对关联用户表
    public function leaderUser()
    {
        return $this->belongsTo('App\Models\User', 'leader');
    }

    // 一对一关联项目报价表
    public function quotation()
    {
        return $this->hasOne('App\Models\QuotationModel', 'design_project_id');
    }

    // 一对多关联项目规划阶段表
    public function designStage()
    {
        return $this->hasMany('App\Models\DesignStage', 'design_project_id');
    }

    // 商务经理
    public function getBusinessManagerValueAttribute()
    {
        return $this->businessManagerUser ? $this->businessManagerUser->getUserName() : null;
    }

    // 项目经理
    public function getLeaderValueAttribute()
    {
        return $this->leaderUser ? $this->leaderUser->getUserName() : null;
    }

    /**
     * 所属领域field 访问修改器
     *
     * @return mixed
     */
    public function getFieldValueAttribute()
    {
        $fields = config('constant.field');
        if (!array_key_exists($this->field, $fields)) {
            return '';
        }
        return $fields[$this->field];
    }

    public function getIndustryValueAttribute()
    {
        $industries = config('constant.industry');
        if (!array_key_exists($this->industry, $industries)) {
            return '';
        }
        return $industries[$this->industry];
    }

    /**
     * 省份访问修改器
     * @return mixed|string
     */
    public function getProvinceValueAttribute()
    {
        return Tools::cityName($this->province);
    }

    /**
     * 城市访问修改器
     * @return mixed|string
     */
    public function getCityValueAttribute()
    {
        return Tools::cityName($this->city);
    }

    /**
     * 区县访问修改器
     * @return mixed|string
     */
    public function getAreaValueAttribute()
    {
        return Tools::cityName($this->area);
    }

    /**
     * 省份访问修改器
     * @return mixed|string
     */
    public function getDesignProvinceValueAttribute()
    {
        return Tools::cityName($this->design_province);
    }

    /**
     * 城市访问修改器
     * @return mixed|string
     */
    public function getDesignCityValueAttribute()
    {
        return Tools::cityName($this->design_city);
    }

    /**
     * 区县访问修改器
     * @return mixed|string
     */
    public function getDesignAreaValueAttribute()
    {
        return Tools::cityName($this->design_area);
    }

    //设计类型
    public function getTypeValueAttribute()
    {
        switch ($this->type) {
            case 1:
                $type_value = '产品设计类型';
                break;
            case 2:
                $type_value = 'UI UX设计类型';
                break;
            default:
                $type_value = '';
        }

        return $type_value;
    }

    //级别
    public function getLevelValueAttribute()
    {
        switch ($this->level) {
            case 1:
                $level_value = '普通';
                break;
            case 2:
                $level_value = '紧急';
                break;
            case 3:
                $level_value = '非常紧急';
                break;
            default:
                $level_value = '';
        }

        return $level_value;
    }

    //设计类别多选
    public function getDesignTypesValueAttribute()
    {
        $item_type = config('constant.item_type');

        $design_types = json_decode($this->design_types, true);
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


    // 判断当前用户有无修改项目信息权限
    public function isPower(int $user_id)
    {
        // 创建用户、商务经理、项目负责人
        if ($this->user_id == $user_id || $this->business_manager == $user_id || $this->leader == $user_id) {
            return true;
        }

        return false;
    }

    // 设计项目项目信息
    public function info()
    {
        $user = User::find($this->user_id);
        if ($user) {
            $user_name = $user->getUserName();
        } else {
            $user_name = '';
        }
        $collect_item = CollectItem::where('item_id' , $this->id)->where('user_id' , $this->user_id)->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'level' => intval($this->level),
            'business_manager' => intval($this->business_manager),
            'business_manager_value' => $this->business_manager_value,
            'leader' => intval($this->leader),
            'leader_value' => $this->leader_value,
            'cost' => $this->cost,
            'workplace' => $this->workplace,
            'field' => intval($this->field),
            'field_value' => $this->field_value,
            'industry' => intval($this->industry),
            'industry_value' => $this->industry_value,
            'start_time' => intval($this->start_time),
            'project_duration' => $this->project_duration,
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'position' => $this->position,
            'phone' => $this->phone,
            'province' => intval($this->province),
            'province_value' => $this->province_value,
            'city' => intval($this->city),
            'area' => intval($this->area),
            'city_value' => $this->city_value,
            'area_value' => $this->area_value,
            'address' => $this->address,
            'user_id' => intval($this->user_id),
            'quotation_id' => intval($this->quotation_id),
            'created_at' => $this->created_at,
            'design_company_name' => $this->design_company_name,
            'design_contact_name' => $this->design_contact_name,
            'design_position' => $this->design_position,
            'design_phone' => $this->design_phone,
            'design_province' => intval($this->design_province),
            'design_province_value' => $this->design_province_value,
            'design_city' => intval($this->design_city),
            'design_area' => intval($this->design_area),
            'design_city_value' => $this->design_city_value,
            'design_area_value' => $this->design_area_value,
            'design_address' => $this->design_address,
            'pan_director_id' => $this->pan_director_id,
            'type' => intval($this->type),
            'type_value' => $this->type_value,
            'design_types' => json_decode($this->design_types),
            'design_types_value' => $this->design_types_value,
            'project_demand' => $this->project_demand,
            'pigeonhole' => $this->pigeonhole,
            'user_name' => $user_name,
            'collect' => $collect_item ? $collect_item->collect : 0
        ];
    }

    /**
     * 移除项目的商务经理
     *
     * @param int $user_id
     * @return bool
     */
    public function removeBusinessManager(int $user_id)
    {
        if ($this->business_manager == $user_id) {
            $this->business_manager = 0;
            return $this->save();
        }

        return true;
    }

    /**
     * 移除项目的项目经理
     *
     * @param int $user_id
     * @return bool
     */
    public function removeLeader(int $user_id)
    {
        if ($this->leader == $user_id) {
            $this->leader = 0;
            return $this->save();
        }

        return true;
    }

}
