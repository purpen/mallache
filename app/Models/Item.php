<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /*user_id	int(10)	否		用户ID
    design_type	tinyint(4)	是		设计类别：1.产品策略；2.产品设计；3.结构设计；4.app设计；5.网页设计；
    status	tinyint(4)	是		状态：1.准备；2.发布；3失败；4成功；5.取消；*/

    protected $table = 'item';

    /**
     * 允许批量赋值属性
     */

    protected $fillable = ['user_id', 'type', 'design_type', 'status'];

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
        return $this->hasMany('App\Models\QuotationModel', 'item_demand_id');
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
                    'design_type' => $item->design_type,
                    'status' => $item->status,
                    'field' => $info->field,
                    'industry' => $info->industry,
                    'name' => $info->name,
                    'product_features' => $info->product_features,
                    'competing_product' => $info->competing_product,
                    'cycle' => $info->cycle,
                    'design_cost' => $info->design_cost,
                    'province' => $info->province,
                    'city' => $info->city,
                    'image' => $info->image,
                ];
                break;
            case 2:
                $info = $item->uDesign;
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'design_type' => $item->design_type,
                    'status' => $item->status,
                    'system' => $info->system,
                    'design_content' => $info->design_content,
                    'name' => $info->name,
                    'stage' => $info->stage,
                    'complete_content' => $info->complete_content,
                    'other_content' => $info->other_content,
                    'design_cost' => $info->design_cost,
                    'province' => $info->province,
                    'city' => $info->city,
                    'image' => $info->image,
                ];
                break;
        }
        return [];
    }

}
