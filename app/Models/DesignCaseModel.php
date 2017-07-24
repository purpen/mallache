<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DesignCaseModel extends BaseModel
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'design_case';

    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'prize',
        'prize_time',
        'mass_production',
        'sales_volume',
        'customer',
        'field',
        'profile',
        'status',
        'type',
        'design_type',
        'industry',
        'other_prize',
        'design_company_id',
        'cover_id'
    ];

    /**
     * 返回
     */
    protected $appends = [
        'field_val',
        'industry_val',
        'type_val',
        'design_type_val',
        'prize_val',
        'case_image',
    ];

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 相对关联设计公司表
     */
    public function DesignCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'design_company_id');
    }


    /**
     * 案例图片
     */
    public function getCaseImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 5, 1);
    }

    /**
     * 封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage((int)$this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 5, 1);
    }

    public function getSalesVolumeValAttribute()
    {
        switch ($this->attributes['sales_volume']) {
            case 1:
                $sales_volume_val = '100-500w';
                break;
            case 2:
                $sales_volume_val = '500-1000w';
                break;
            case 3:
                $sales_volume_val = '1000-5000w';
                break;
            case 4:
                $sales_volume_val = '5000-10000w';
                break;
            case 5:
                $sales_volume_val = '10000w以上';
                break;
            default:
                $sales_volume_val = '';
        }
        return $sales_volume_val;
    }

    //判断设计类型
    public function getTypeValAttribute()
    {
        return $this->attributes['type'] == 1 ? '产品设计' : 'UI UX 设计';
    }

    //判断设计类别
    public function getDesignTypeValAttribute()
    {
        if ($this->attributes['type'] == 1) {
            switch ($this->attributes['design_type']) {
                case 1:
                    $design_type_val = '产品策略';
                    break;
                case 2:
                    $design_type_val = '产品外观设计';
                    break;
                case 3:
                    $design_type_val = '结构设计';
                    break;
                default:
                    $design_type_val = '';
            }
        } else {
            switch ($this->attributes['design_type']) {
                case 1:
                    $design_type_val = 'app设计';
                    break;
                case 2:
                    $design_type_val = '网页设计';
                    break;
                default:
                    $design_type_val = '';
            }

        }
        return $design_type_val;
    }


    //判断领域
    public function getFieldValAttribute()
    {
        if ($this->attributes['type'] == 1) {
            $key = $this->attributes['field'];
            if (array_key_exists($key, config('constant.field'))) {
                $prize_val = config('constant.field')[$key];
                return $prize_val;

            }
            return '';
        }
    }

    //判断行业
    public function getIndustryValAttribute()
    {
        if ($this->attributes['type'] == 1) {
            $key = $this->attributes['industry'];
            if (array_key_exists($key, config('constant.industry'))) {
                $prize_val = config('constant.industry')[$key];
                return $prize_val;

            }
            return '';
        }
    }

    //判断奖项
    public function getPrizeValAttribute()
    {
        $key = $this->attributes['prize'];
        if (array_key_exists($key, config('constant.prize'))) {
            $prize_val = config('constant.prize')[$key];
            return $prize_val;

        }
        return '';
    }

}
