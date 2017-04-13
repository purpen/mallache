<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDesign extends Model
{
    protected $table = 'product_design';

    protected $fillable = [
        'item_id',
        'name',
//        'target_retail_price',
//        'annual_sales',
//        'service_life',
//        'competitive_brand',
        'competing_product',
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
}
