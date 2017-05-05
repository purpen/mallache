<?php

namespace App\Models;

use App\Helper\Tools;
use Illuminate\Database\Eloquent\Model;

class UDesign extends Model
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
        'cycle_value'
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
        return explode('&', $key);
    }

    public function getDesignCostValueAttribute()
    {
        //设计费用：1、1万以下；2、1-5万；3、5-10万；4.10-20；5、20-30；6、30-50；7、50以上
        switch ($this->design_cost){
            case 1:
                $design_cost_value = '1万以下';
                break;
            case 2:
                $design_cost_value = '1-5万之间';
                break;
            case 3:
                $design_cost_value = '5-10万之间';
                break;
            case 4:
                $design_cost_value = '10-20万之间';
                break;
            case 5:
                $design_cost_value = '20-30万之间';
                break;
            case 6:
                $design_cost_value = '30-50万之间';
                break;
            case 7:
                $design_cost_value = '50万以上';
                break;
            default:
                $design_cost_value = '';
        }
        return $design_cost_value;
    }

    public function getCycleValueAttribute()
    {
        switch ($this->cycle){
            case 1:
                $cycle_value = '1个月内';
                break;
            case 2:
                $cycle_value = '1-2个月';
                break;
            case 3:
                $cycle_value = '2个月';
                break;
            case 4:
                $cycle_value = '2-4个月';
                break;
            case 5:
                $cycle_value = '其他';
                break;
            default:
                $cycle_value = '';
        }
        return $cycle_value;
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
