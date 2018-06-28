<?php

namespace App\Models;

use App\Events\ItemStatusEvent;
use App\Helper\ItemCommissionAction;
use App\Helper\NumberToHanZi;
use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Item extends BaseModel
{
    use SoftDeletes;

    protected $table = 'item';

    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['stage_status', 'user_id', 'type', 'design_type', 'company_name', 'company_abbreviation', 'company_size', 'company_web', 'company_province', 'company_city', 'company_area', 'address', 'contact_name', 'phone', 'email', 'status', 'contract_id', 'position', 'design_types', 'source', 'name'];

    /**
     * 添加返回字段
     */
    protected $appends = [
        'type_value',
        'company_province_value',
        'company_city_value',
        'company_area_value',
        'status_value',
        'company_size_value',
        'design_types_value',
        'industry_value',
        'cycle_value',
        'design_cost_value',
        'province_value',
        'city_value',
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

    //一对一关联 平面设计表
    public function graphicDesign()
    {
        return $this->hasOne('App\Models\GraphicDesign', 'item_id');
    }

    //一对一关联 H5设计表
    public function h5Design()
    {
        return $this->hasOne('App\Models\H5Design', 'item_id');
    }

    //一对一关联 包装设计表
    public function packDesign()
    {
        return $this->hasOne('App\Models\PackDesign', 'item_id');
    }

    //一对一关联 插画设计表
    public function illustrationDesign()
    {
        return $this->hasOne('App\Models\IllustrationDesign', 'item_id');
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 一对-关联报价
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

    //一对多关联支付单
    public function payOrder()
    {
        return $this->hasMany('App\Models\PayOrder', 'item_id');
    }

    // 一对一关联评价表
    public function evaluate()
    {
        return $this->hasOne('App\Models\Evaluate', 'item_id');
    }

    // 一对多关联云盘层级目录pan_director
    public function panDirector()
    {
        return $this->hasMany('App\Models\PanDirector', 'item_id');
    }

    // 一对多关联项目阶段
    public function itemStage()
    {
        return $this->hasMany('App\Models\ItemStage', 'item_id');
    }

    // 一对多关联发票invoice
    public function invoice()
    {
        return $this->hasMany('App\Models\Invoice', 'item_id');
    }

    //创建需求表
    public static function createItem($user_id, $name, $source = 0)
    {
        $item = Item::query()
            ->create([
                'user_id' => $user_id,
                'status' => 1,
                'type' => 0,
                'design_type' => 0,
                'source' => $source,
                'name' => $name,
                'stage_status' => 1,
            ]);
        if ($item) {
            event(new ItemStatusEvent($item));
            return $item;
        } else {
            return false;
        }
    }

    /**
     * 判断item对应的详细信息
     *
     * @return array
     */
    public function itemInfo()
    {
        $item = $this;

        $price = number_format($item->price, 2, '.', '');
        $commission = ItemCommissionAction::getCommission($item);

        switch ((int)$item->type) {
            case 0:
                goto a1;
                break;
            case 1:
                $info = $item->productDesign;
                if (!$info) {
                    goto a1;
                }
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_types' => json_decode($item->design_types),
                    'design_types_value' => $item->design_types_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,

                    'field' => $info->field,
                    'field_value' => $info->field_value,
                    'industry' => $item->industry,
                    'industry_value' => $item->industry_value,
                    'name' => $item->name,
                    'product_features' => $info->product_features,
                    'competing_product' => explode('&', $info->competing_product),
                    'cycle' => $item->cycle,
                    'cycle_value' => $item->cycle_value,
                    'design_cost' => $item->design_cost,
                    'design_cost_value' => $item->design_cost_value,
                    'province' => $item->item_province,
                    'city' => $item->item_city,
                    'province_value' => $item->province_value,
                    'city_value' => $item->city_value,
                    'image' => $info->image,

                    'price' => $price,
                    'price_han' => NumberToHanZi::numberToH($price),
                    'commission' => $commission,
                    'commission_han' => NumberToHanZi::numberToH($commission),
                    'commission_rate' => $item->commission_rate,

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
                    'position' => $item->position,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'stage_status' => (int)$item->stage_status,
                    'created_at' => $item->created_at,
                    'source' => $item->source,
                    'tax_rate' => $item->tax_rate,
                    'tax' => $item->tax,
                ];
                break;
            case 2:
                $info = $item->uDesign;
                if (!$info) {
                    goto a1;
                }
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_types' => json_decode($item->design_types),
                    'design_types_value' => $item->design_types_value,
                    'industry' => $item->industry,
                    'industry_value' => $item->industry_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,
                    'name' => $item->name,
                    'stage' => $info->stage,
                    'stage_value' => $info->stage_value,
                    'complete_content' => $info->complete_content,
                    'other_content' => $info->other_content,
                    'design_cost' => $item->design_cost,
                    'design_cost_value' => $item->design_cost_value,
                    'province' => $item->item_province,
                    'city' => $item->item_city,
                    'province_value' => $item->province_value,
                    'city_value' => $item->city_value,
                    'image' => $info->image,

                    'price' => $price,
                    'price_han' => NumberToHanZi::numberToH($price),
                    'commission' => $commission,
                    'commission_han' => NumberToHanZi::numberToH($commission),
                    'commission_rate' => $item->commission_rate,

                    'stage_status' => (int)$item->stage_status,
                    'cycle' => $item->cycle,
                    'cycle_value' => $item->cycle_value,

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
                    'position' => $item->position,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'created_at' => $item->created_at,
                    'source' => $item->source,
                    'product_features' => $info->product_features,

                ];
                break;
            case 3:
                $info = $item->graphicDesign;
                if (!$info) {
                    goto a1;
                }
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_types' => json_decode($item->design_types),
                    'design_types_value' => $item->design_types_value,
                    'industry' => $item->industry,
                    'industry_value' => $item->industry_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,
                    'name' => $item->name,
                    'design_cost' => $item->design_cost,
                    'design_cost_value' => $item->design_cost_value,
                    'province' => $item->item_province,
                    'city' => $item->item_city,
                    'province_value' => $item->province_value,
                    'city_value' => $item->city_value,
                    'image' => $info->image,

                    'price' => $price,
                    'price_han' => NumberToHanZi::numberToH($price),
                    'commission' => $commission,
                    'commission_han' => NumberToHanZi::numberToH($commission),
                    'commission_rate' => $item->commission_rate,

                    'stage_status' => (int)$item->stage_status,
                    'cycle' => $item->cycle,
                    'cycle_value' => $item->cycle_value,

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
                    'position' => $item->position,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'created_at' => $item->created_at,
                    'source' => $item->source,
                    'tax_rate' => $item->tax_rate,
                    'tax' => $item->tax,
                    'product_features' => $info->product_features,
                    'present_situation' => (int)$info->present_situation,
                    'existing_content' => (int)$info->existing_content,
                ];
                break;
            case 4:
                $info = $item->h5Design;
                if (!$info) {
                    goto a1;
                }
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_types' => json_decode($item->design_types),
                    'design_types_value' => $item->design_types_value,
                    'industry' => $item->industry,
                    'industry_value' => $item->industry_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,
                    'name' => $item->name,
                    'design_cost' => $item->design_cost,
                    'design_cost_value' => $item->design_cost_value,
                    'province' => $item->item_province,
                    'city' => $item->item_city,
                    'province_value' => $item->province_value,
                    'city_value' => $item->city_value,
                    'image' => $info->image,

                    'price' => $price,
                    'price_han' => NumberToHanZi::numberToH($price),
                    'commission' => $commission,
                    'commission_han' => NumberToHanZi::numberToH($commission),
                    'commission_rate' => $item->commission_rate,

                    'stage_status' => (int)$item->stage_status,
                    'cycle' => $item->cycle,
                    'cycle_value' => $item->cycle_value,

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
                    'position' => $item->position,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'created_at' => $item->created_at,
                    'source' => $item->source,
                    'tax_rate' => $item->tax_rate,
                    'tax' => $item->tax,
                    'product_features' => $info->product_features,
                    'present_situation' => (int)$info->present_situation,
                ];
                break;
            case 5:
                $info = $item->packDesign;
                if (!$info) {
                    goto a1;
                }
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_types' => json_decode($item->design_types),
                    'design_types_value' => $item->design_types_value,
                    'industry' => $item->industry,
                    'industry_value' => $item->industry_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,
                    'name' => $item->name,
                    'design_cost' => $item->design_cost,
                    'design_cost_value' => $item->design_cost_value,
                    'province' => $item->item_province,
                    'city' => $item->item_city,
                    'province_value' => $item->province_value,
                    'city_value' => $item->city_value,
                    'image' => $info->image,

                    'price' => $price,
                    'price_han' => NumberToHanZi::numberToH($price),
                    'commission' => $commission,
                    'commission_han' => NumberToHanZi::numberToH($commission),
                    'commission_rate' => $item->commission_rate,

                    'stage_status' => (int)$item->stage_status,
                    'cycle' => $item->cycle,
                    'cycle_value' => $item->cycle_value,

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
                    'position' => $item->position,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'created_at' => $item->created_at,
                    'source' => $item->source,
                    'tax_rate' => $item->tax_rate,
                    'tax' => $item->tax,
                    'product_features' => $info->product_features,
                    'present_situation' => (int)$info->present_situation,
                    'existing_content' => (int)$info->existing_content,
                ];
                break;
            case 6:
                $info = $item->illustrationDesign;
                if (!$info) {
                    goto a1;
                }
                return [
                    'id' => $item->id,
                    'type' => (int)$item->type,
                    'type_value' => $item->type_value,
                    'design_types' => json_decode($item->design_types),
                    'design_types_value' => $item->design_types_value,
                    'industry' => $item->industry,
                    'industry_value' => $item->industry_value,
                    'status' => $item->status,
                    'status_value' => $item->status_value,
                    'design_status_value' => $item->design_status_value,
                    'design_company_id' => $item->design_company_id,
                    'name' => $item->name,
                    'design_cost' => $item->design_cost,
                    'design_cost_value' => $item->design_cost_value,
                    'province' => $item->item_province,
                    'city' => $item->item_city,
                    'province_value' => $item->province_value,
                    'city_value' => $item->city_value,
                    'image' => $info->image,

                    'price' => $price,
                    'price_han' => NumberToHanZi::numberToH($price),
                    'commission' => $commission,
                    'commission_han' => NumberToHanZi::numberToH($commission),
                    'commission_rate' => $item->commission_rate,

                    'stage_status' => (int)$item->stage_status,
                    'cycle' => $item->cycle,
                    'cycle_value' => $item->cycle_value,

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
                    'position' => $item->position,
                    'contact_name' => $item->contact_name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'created_at' => $item->created_at,
                    'source' => $item->source,
                    'tax_rate' => $item->tax_rate,
                    'tax' => $item->tax,
                    'product_features' => $info->product_features,
                    'present_situation' => (int)$info->present_situation,
                ];
                break;
        }

        return [];

        a1:
        return [
            'id' => $item->id,
            'type' => (int)$item->type,
            'type_value' => $item->type_value,
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
            'created_at' => $item->created_at,
            'design_cost' => null,
            'cycle' => null,
            'design_types' => json_decode($item->design_types),
            'design_types_value' => $item->design_types_value,
            'source' => $item->source,
            'name' => $item->name,
        ];
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

    //设计类别(准备停用)
    /*public function getDesignTypeValueAttribute()
    {
        $item_type = config('constant.item_type');

        if (array_key_exists($this->type, $item_type)) {
            if (array_key_exists($this->design_type, $item_type[$this->type])) {
                return $item_type[$this->type][$this->design_type];
            }
        }

        return '';
    }*/

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


    //公司规模
    public function getCompanySizeValueAttribute()
    {
        switch ($this->company_size) {
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

        if (!array_key_exists($this->status, $demand_item_status)) {
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

        if (!array_key_exists($this->status, $demand_item_status)) {
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
        if (empty($item_count)) {
            $item = Item::find($item_id);
            $item->status = -2;
            $item->save();

            //触发项目状态变更事件
            event(new ItemStatusEvent($item));
        }
    }


    public function getStatusTimeAttribute($key)
    {
        return json_decode($key, true);
    }

    /**
     * 项目状态变化记录方法
     *
     * @param int $status 项目状态
     */
    public function statusTime(int $status)
    {
        $status_time_arr = [
            -3 => '',
            -2 => '',
            -1 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            11 => '',
            15 => '',
            18 => '',
            22 => '',
        ];

        $status_time = $this->status_time;
        if (!empty($status_time)) {
            $status_time_arr = $status_time;
        }

        $status_time_arr[$status] = date("Y-m-d h:i:s");
        $this->status_time = (string)json_encode($status_time_arr);
        $this->save();
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
