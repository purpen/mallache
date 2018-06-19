<?php

namespace App\Models;

use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;

class UDesign extends BaseModel
{
    protected $table = 'u_design';

    protected $appends = [
        'image',
        'system_value',
        'design_content_value',
        'stage_value',
        'design_cost_value',
        'province_value',
        'city_value',
        'cycle_value',
        'industry_value',
    ];

    //允许批量赋值的属性
    protected $fillable = [
        'item_id',
//        'system',
//        'design_content',
//        'page_number',
        'name',
        'stage',
        'complete_content',
        'other_content',
//        'style',
//        'start_time',
        'cycle',
        'design_cost',
        'province',
        'city',
//        'summary',
//        'artificial',
        'industry',
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

    public function getSystemValueAttribute()
    {
        switch ($this->system){
            case 1:
                $system_value = 'IOS';
                break;
            case 2:
                $system_value = '安卓';
                break;
            default:
                $system_value = '';
        }
        return $system_value;
    }

    public function getDesignContentValueAttribute()
    {
        switch ($this->design_content){
            case 1:
                $design_content_value = '视觉设计';
                break;
            case 2:
                $design_content_value = '交互设计';
                break;
            default:
                $design_content_value = '';
        }
        return $design_content_value;
    }

    //阶段访问修改器
    public function getStageValueAttribute()
    {
        //1、已有app／网站，需重新设计；2、没有app／网站，需要全新设计；
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

    //以完成设计流程 访问修改器
    public function getCompleteContentAttribute($key)
    {
        if(empty($key)){
            return [];
        }else{
            return explode('&', (int)$key);
        }

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

    public function getCycleValueAttribute()
    {
        $item_cycle = config('constant.item_cycle');
        if(!array_key_exists($this->cycle, $item_cycle)){
            return '';
        }
        return $item_cycle[$this->cycle];
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

    public function getIndustryValueAttribute()
    {
        $industries = config('constant.industry');
        if(!array_key_exists($this->industry, $industries)){
            return '';
        }
        return $industries[$this->industry];
    }

}
