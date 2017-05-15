<?php

namespace App\Models;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Item extends Model
{
    protected $table = 'item';

    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['stage_status', 'user_id', 'type', 'design_type', 'company_name','company_abbreviation', 'company_size', 'company_web', 'company_province', 'company_city', 'company_area', 'address', 'contact_name', 'phone', 'email', 'status' , 'contract_id'];

    /**
     * 添加返回字段
     */
    protected $appends = [
        'type_value',
        'design_type_value',
        'company_province_value',
        'company_city_value',
        'company_area_value',
        'status_value',
    ];

    //一对一关联UX UI设计表
    public function uDesign()
    {
        return $this->hasOne('App\Models\UDesign', 'item_id');
    }

    //一对一关联 产品设计表
    public function productDesign()
    {
        return $this->hasOne('App\Models\ProductDesign', 'item_id');
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 一对多关联报价
     */
    public function quotation()
    {
        return $this->belongsTo('App\Models\QuotationModel', 'quotation_id');
    }

    /**
     * 一对多关联 推荐关联表
     */
    public function itemRecommend()
    {
        return $this->hasMany('App\Models\ItemRecommend', 'item_id');
    }

    //一对一关联 合同表
    public function contract()
    {
        return $this->hasOne('App\Models\Contract', 'item_demand_id');
    }

    //一对多 相对关联设计公司
    public function designCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'design_company_id');
    }

    /**
     * 判断item对应的详细信息
     *
     * @return array
     */
    public function itemInfo()
    {
        $item = $this;
        switch ((int)$item->type){
            case 0:
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_type' => (int)$item->design_type,
                    'design_type_value' => $item->design_type_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'price' => floatval($item->price),
                    'company_name' => $item->company_name,
                    'company_abbreviation' => $item->company_abbreviation,
                    'company_size' => $item->company_size,
                    'company_size_value' => $item->company_size_value,
                    'company_web' => $item->company_web,
                    'company_province' => $item->company_province,
                    'company_city' => $item->company_city,
                    'company_area' => $item->company_area,
                    'company_province_value' => $item->company_province_value,
                    'company_city_value' => $item->company_city_value,
                    'company_area_value' => $item->company_area_value,
                    'address' => $item->address,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'stage_status' => (int)$item->stage_status,
                    'created_at' => $item->created_at->format("Y-m-d"),
                ];
            case 1:
                $info = $item->productDesign;
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_type' => $item->design_type,
                    'design_type_value' => $item->design_type_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,
                    'field' => $info->field,
                    'field_value' => $info->field_value,
                    'industry' => $info->industry,
                    'industry_value' => $info->industry_value,
                    'name' => $info->name,
                    'product_features' => $info->product_features,
                    'competing_product' => explode('&', $info->competing_product),
                    'cycle' => $info->cycle,
                    'cycle_value' => $info->cycle_value,
                    'design_cost' => $info->design_cost,
                    'design_cost_value' => $info->design_cost_value,
                    'province' => $info->province,
                    'city' => $info->city,
                    'province_value' => $info->province_value,
                    'city_value' => $info->city_value,
                    'image' => $info->image,
                    'price' => floatval($item->price),

                    'company_name' => $item->company_name,
                    'company_abbreviation' => $item->company_abbreviation,
                    'company_size' => $item->company_size,
                    'company_size_value' => $item->company_size_value,
                    'company_web' => $item->company_web,
                    'company_province' => $item->company_province,
                    'company_city' => $item->company_city,
                    'company_area' => $item->company_area,
                    'company_province_value' => $item->company_province_value,
                    'company_city_value' => $item->company_city_value,
                    'company_area_value' => $item->company_area_value,
                    'address' => $item->address,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'stage_status' => (int)$item->stage_status,
                    'created_at' => $item->created_at->format("Y-m-d"),
                ];
                break;
            case 2:
                if(!$info = $item->uDesign){
                    return [];
                }
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_type' => (int)$item->design_type,
                    'design_type_value' => $item->design_type_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,
                    'name' => $info->name,
                    'stage' => $info->stage,
                    'stage_value' => $info->stage_value,
                    'complete_content' => $info->complete_content,
                    'other_content' => $info->other_content,
                    'design_cost' => $info->design_cost,
                    'design_cost_value' => $info->design_cost_value,
                    'province' => $info->province,
                    'city' => $info->city,
                    'province_value' => $info->province_value,
                    'city_value' => $info->city_value,
                    'image' => $info->image,
                    'price' => floatval($item->price),
                    'stage_status' => (int)$item->stage_status,
                    'cycle' => $info->cycle,
                    'cycle_value' => $info->cycle_value,

                    'company_name' => $item->company_name,
                    'company_abbreviation' => $item->company_abbreviation,
                    'company_size' => $item->company_size,
                    'company_size_value' => $item->company_size_value,
                    'company_web' => $item->company_web,
                    'company_province' => $item->company_province,
                    'company_city' => $item->company_city,
                    'company_area' => $item->company_area,
                    'company_province_value' => $item->company_province_value,
                    'company_city_value' => $item->company_city_value,
                    'company_area_value' => $item->company_area_value,
                    'address' => $item->address,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'created_at' => $item->created_at->format("Y-m-d"),
                ];
                break;
        }
        return [];
    }

    //设计类型
    public function getTypeValueAttribute()
    {
        switch ($this->type){
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

    //设计类别
    public function getDesignTypeValueAttribute()
    {
        if($this->type == 1){
            switch ($this->design_type){
                case 1:
                    $design_type_value = '产品策略';
                    break;
                case 2:
                    $design_type_value = '产品设计';
                    break;
                case 3:
                    $design_type_value = '结构设计';
                    break;
                default:
                    $design_type_value = '';
            }
        }else{
            switch ($this->design_type){
                case 1:
                    $design_type_value = 'app设计';
                    break;
                case 2:
                    $design_type_value = '网页设计';
                    break;
                default:
                    $design_type_value = '';
            }
        }

        return $design_type_value;
    }

    //UI/UX设计阶段 1、已有app／网站，需重新设计；2、没有app／网站，需要全新设计；
    public function getStageValueAttribute()
    {
        switch ($this->stage){
            case 1:
                $stage_value = '已有app／网站，需重新设计';
                break;
            case 2:
                $stage_value = '没有app／网站，需要全新设计';
                break;
            default:
                $stage_value = '';
        }

        return $stage_value;
    }

    //已有项目设计内容格式化输出 已完成设计内容：1.流程图；2.线框图；3.页面内容；4.产品功能需求点；
    public function getCompleteContentValueAttribute()
    {
        switch ($this->complete_content){
            case 1:
                $complete_content_value = '流程图';
                break;
            case 2:
                $complete_content_value = '线框图';
                break;
            case 3:
                $complete_content_value = '页面内容';
                break;
            case 4:
                $complete_content_value = '产品功能需求点';
                break;
            case 5:
                $complete_content_value = $this->other_content;
                break;
            default:
                $complete_content_value = ' ';
        }
    }

    //公司规模
    public function getCompanySizeValueAttribute()
    {
        switch ($this->company_size){
            case 1:
                $company_size_val = '10人以下';
                break;
            case 2:
                $company_size_val = '10-50人之间';
                break;
            case 3:
                $company_size_val = '50-100人之间';
                break;
            case 4:
                $company_size_val = '100人以上';
                break;
            case 5:
                $company_size_val = '初创公司';
                break;
            default:
                $company_size_val = '';
        }
        return $company_size_val;
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

    /**
     * 项目状态说明(需求公司)
     */
    public function getStatusValueAttribute()
    {
        $demand_item_status = config('constant.demand_item_status');

        if(!array_key_exists($this->status, $demand_item_status)){
            return '';
        }
        return $demand_item_status[$this->status];
    }

    /**
     * 项目状态说明（设计公司））
     */
    public function getDesignStatusValueAttribute()
    {
        $demand_item_status = config('constant.design_item_status');

        if(!array_key_exists($this->status, $demand_item_status)){
            return '';
        }
        return $demand_item_status[$this->status];
    }

    /**
     * 判断项目是否匹配失败
     */
    public function itemIsFail(int $item_id)
    {
        //尚在匹配项目数量
        $item_count = ItemRecommend::where('item_id', $item_id)
            ->where('item_status', '!=', -1)
            ->where('design_company_status', '!=', -1)
            ->count();
        //匹配失败项目状态修改为匹配失败（2）
        if(empty($item_count)){
            $item = Item::find($item_id);
            $item->status = -2;
            $item->save();

            //触发项目状态变更事件
            event(new ItemStatusEvent($item));
        }
    }

    //项目状态变更为项目款已托管
    public function itemStatusChange($status = 8)
    {
        $this->status = $status;
        $this->save();

        event(new ItemStatusEvent($this));
    }
}
