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
            $a['price'] = floatval($plan->price);
            $a['summary'] = $plan->summary;

            $arr[] = $a;
        }

        return $arr;
    }

    // 报价单详情
    public function info()
    {
        return [
            'id' => $this->id,
            'company_name' => $this->designProject->company_name,
            'contact_name' => $this->designProject->contact_name,
            'phone' => $this->designProject->phone,
            'position' => $this->designProject->position,
            'province' => $this->designProject->province,
            'province_value' => $this->designProject->province_value,
            'city' => $this->designProject->city,
            'city_value' => $this->designProject->city_value,
            'area' => $this->designProject->area,
            'area_value' => $this->designProject->area_value,
            'address' => $this->designProject->address,

            'design_company_name' => $this->designProject->design_company_name,
            'design_contact_name' => $this->designProject->design_contact_name,
            'design_position' => $this->designProject->design_position,
            'design_phone' => $this->designProject->design_phone,
            'design_province' => $this->designProject->design_province,
            'design_province_value' => $this->designProject->design_province_value,
            'design_city' => $this->designProject->design_city,
            'design_city_value' => $this->designProject->design_city_value,
            'design_area' => $this->designProject->design_area,
            'design_area_value' => $this->designProject->design_area_value,
            'design_address' => $this->designProject->design_address,

            'project_name' => $this->designProject->name,
            'summary' => $this->summary,
            'plan' => $this->getProjectPlan(),
            'is_tax' => intval($this->is_tax),
            'is_invoice' => intval($this->is_invoice),
            'tax_rate' => intval($this->tax_rate),
            'total_price' => floatval($this->total_price),
            'price' => floatval($this->price),
            'asset' => AssetModel::getImageUrl($this->id, 30),

            'item_demand_id' => intval($this->item_demand_id),
            'design_company_id' => intval($this->design_company_id),
        ];
    }

}
