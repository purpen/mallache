<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommuneSummary extends BaseModel
{
    protected $table = 'commune_summaries';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'status',
        'title',
        'content',
        'location',
        'expire_time',
        'other_realname',
    ];

    /**
     * 案例图片
     */
    public function getCommuneImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 29, 1);
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
