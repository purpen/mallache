<?php

namespace App\Models;

//设计公司项目管理
use App\Helper\Tools;

class DesignProject extends BaseModel
{
    protected $table = 'design_project';

    // 批量赋值黑名单
    protected $guarded = ['user_id', 'type', 'status', 'token'];

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
    public function getCompanyProvinceValueAttribute()
    {
        return Tools::cityName($this->company_province);
    }

    /**
     * 城市访问修改器
     * @return mixed|string
     */
    public function getCompanyCityValueAttribute()
    {
        return Tools::cityName($this->company_city);
    }

    /**
     * 区县访问修改器
     * @return mixed|string
     */
    public function getCompanyAreaValueAttribute()
    {
        return Tools::cityName($this->company_area);
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'level' => $this->level,
            'business_manager' => $this->business_manager,
            'business_manager_value' => $this->business_manager_value,
            'leader' => $this->leader,
            'leader_value' => $this->leader_value,
            'cost' => $this->cost,
            'workplace' => $this->workplace,
            'type_value' => $this->type_value,
            'field' => $this->field,
            'field_value' => $this->field_value,
            'industry' => $this->industry,
            'industry_value' => $this->industry_value,
            'start_time' => $this->start_time,
            'project_duration' => $this->project_duration,
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'position' => $this->position,
            'phone' => $this->phone,
            'province' => $this->province,
            'province_value' => $this->province_value,
            'city' => $this->city,
            'area' => $this->area,
            'city_value' => $this->city_value,
            'area_value' => $this->area_value,
            'address' => $this->address,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
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
