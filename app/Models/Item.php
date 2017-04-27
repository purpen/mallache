<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Item extends Model
{
    protected $table = 'item';

    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['user_id', 'type', 'design_type', 'company_name','company_abbreviation', 'company_size', 'company_web', 'company_province', 'company_city', 'company_area', 'address', 'contact_name', 'phone', 'email'];

    /**
     * 添加返回字段
     */
    protected $appends = ['type_value', 'design_type_value'];

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

    /**
     * 判断item对应的详细信息
     *
     * @return array
     */
    public function itemInfo()
    {
        $item = $this;
        switch ((int)$item->type){
            case 1:
                $info = $item->productDesign;
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'type_value' => $item->type_value,
                    'design_type' => $item->design_type,
                    'design_type_value' => $item->design_type_value,
                    'status' => $item->status,
                    'field' => $info->field,
                    'field_value' => $info->field_value,
                    'industry' => $info->industry,
                    'industry_value' => $info->industry_value,
                    'name' => $info->name,
                    'product_features' => $info->product_features,
                    'competing_product' => $info->competing_product,
                    'cycle' => $info->cycle,
                    'cycle_value' => $info->cycle_value,
                    'design_cost' => $info->design_cost,
                    'design_cost_value' => $info->design_cost_value,
                    'city' => $info->city,
                    'image' => $info->image,
                    'price' => floatval($item->price),

                    'company_name' => $item->company_name,
                    'company_abbreviation' => $item->company_abbreviation,
                    'company_size' => $item->company_size,
                    'company_web' => $item->company_web,
                    'company_province' => $item->company_province,
                    'company_city' => $item->company_city,
                    'company_area' => $item->company_area,
                    'address' => $item->address,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                ];
                break;
            case 2:
                if(!$info = $item->uDesign){
                    return [];
                }
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'type_value' => $item->type_value,
                    'design_type' => $item->design_type,
                    'design_type_value' => $item->design_type_value,
                    'status' => $item->status,
//                    'system' => $info->system,
//                    'system_value' => $info->system_value,
//                    'design_content' => $info->design_content,
//                    'design_content_value' => $info->design_content_value,
                    'name' => $info->name,
                    'stage' => $info->stage,
                    'stage_value' => $info->stage_value,
                    'complete_content' => $info->complete_content,
                    'complete_content_value' => $info->complete_content_value,
                    'other_content' => $info->other_content,
                    'design_cost' => $info->design_cost,
                    'design_cost_value' => $info->design_cost_value,
                    'province' => $info->province,
                    'city' => $info->city,
                    'image' => $info->image,
                    'price' => floatval($item->price),
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
                $type_value = 'UI UX 设计类型';
                break;
            default:
                $type_value = '';
        }

        return $type_value;
    }

    //设计类别
    public function getDesignTypeValueAttribute()
    {
        if($this->type === 1){
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

    //创建需求表
    public function createItem($user_id)
    {
        if(self::create(['user_id' => $user_id, 'type' => 0, 'design_type' => 0])){
            return true;
        }else{
            Log::error('创建需求表时报');
            return false;
        };
    }
}
