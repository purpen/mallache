<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationModel extends BaseModel
{
    /**
     *与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'quotation';


    /**
     * 允许批量赋值字段
     * @var array
     */
    protected $fillable = ['user_id', 'item_demand_id', 'design_company_id', 'price', 'summary', 'status', 'is_tax',
        'is_invoice',
        'tax_rate',
        'design_project_id',
        'total_price',
        'design_company_name',
        'design_contact_name',
        'design_phone',
        'design_address'
    ];

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * 一对一关联 推荐关联表
     */
    public function itemRecommend()
    {
        return $this->hasOne('App\Models\ItemRecommend', 'quotation_id');
    }

    /**
     * 一对一关联 项目表
     */
    public function item()
    {
        return $this->hasOne('App\Models\Item', 'quotation_id');
    }

    /**
     * 一对一相对关联 设计管理项目
     */
    public function designProject()
    {
        return $this->belongsTo('App\Models\DesignProject', 'design_project_id');
    }


    /**
     * 一对多关联设计计划表
     */
    public function projectPlan()
    {
        return $this->hasMany('App\Models\ProjectPlan', 'quotation_id');
    }

    /**
     * 相对关联设计公司
     */
    public function designCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'design_company_id');
    }

    // 获取项目报价计划
    public function getProjectPlan()
    {
        $plans = $this->projectPlan;
        $arr = [];
        foreach ($plans as $plan) {
            $a = [];
            $a['content'] = $plan->content;
            $a['arranged'] = json_decode($plan->arranged, true);
            $a['duration'] = $plan->duration;
            $a['price'] = $plan->price;
            $a['summary'] = $plan->summary;

            $arr[] = $a;
        }

        return $arr;
    }

}
