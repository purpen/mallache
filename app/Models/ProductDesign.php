<?php

namespace App\Models;

use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;

class ProductDesign extends BaseModel
{
    protected $table = 'product_design';

    protected $appends = [
        'field_value',
        'industry_value',
        'cycle_value',
        'design_cost_value',
        'province_value',
        'city_value',
    ];

    protected $fillable = [
        'item_id',
        'name',
//        'target_retail_price',
//        'annual_sales',
//        'service_life',
//        'competitive_brand',
//        'competing_product',
        'cycle',
//        'target_brand',
//        'brand_positioning',
//        'product_size',
//        'reference_model',
//        'percentage_completion',
//        'special_technical_require',
        'design_cost',
        'province',
        'city',
//        'divided_into_cooperation',
//        'stock_cooperation',
//        'summary',
        'product_features',
        'field',
        'industry'
    ];

    //一对一关联项目表
    public function item()
    {
        return $this->belongsTo('App\Models\item', 'item_id');
    }

    /**
     * 获取图片url
     *
     * @return array
     */
    public function getImageAttribute()
    {
        return AssetModel::getImageUrl($this->item_id, 4, 1);
    }

    /**
     * 所属领域field 访问修改器
     *
     * @return mixed
     */
    public function getFieldValueAttribute()
    {
        $fields = config('constant.field');
        if(!array_key_exists($this->field, $fields)){
            return '';
        }
        return $fields[$this->field];
    }

    public function getIndustryValueAttribute()
    {
        $industries = config('constant.industry');
        if(!array_key_exists($this->industry, $industries)){
            return '';
        }
        return $industries[$this->industry];
    }

    public function getCycleValueAttribute()
    {
        $item_cycle = config('constant.item_cycle');
        if(!array_key_exists($this->cycle, $item_cycle)){
            return '';
        }

        return $item_cycle[$this->cycle];
    }

    public function getDesignCostValueAttribute()
    {
        //设计费用：1、1-5万；2、5-10万；3.10-20；4、20-30；5、30-50；6、50以上
        switch ($this->design_cost){
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
        return Tools::cityName($this->province) ?? "";
    }

    //城市访问修改器
    public function getCityValueAttribute()
    {
        return Tools::cityName($this->city) ?? "";
    }

}
