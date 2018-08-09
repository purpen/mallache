<?php
namespace App\Models;

class Evaluate extends BaseModel
{
    protected $table = 'evaluate';

    protected $fillable = [
        'demand_company_id',
        'design_company_id',
        'item_id',
        'content',
        'score',
        'platform_score',
        'user_score',
    ];

    // 一对一 相对关联项目表
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'user_id');
    }

    public function get_one($itme_id)
    {
        return $this::where('item_id',$itme_id)->first();
    }
}