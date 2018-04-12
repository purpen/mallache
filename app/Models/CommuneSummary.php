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
    ];

    /**
     * 案例图片
     */
    public function getCommuneImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 29, 1);
    }
}
