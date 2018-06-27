<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IllustrationDesign extends BaseModel
{
    protected $table = 'illustration_design';

    //允许批量赋值的属性
    protected $fillable = [
        'item_id',
        'product_features',
        'present_situation',
        'status',
    ];

    //一对一关联项目表
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    /**
     * 获取图片url
     *
     * @return array
     */
    public function getImageAttribute()
    {
        return AssetModel::getImageUrl($this->item_id, 4, 1);
    }}
