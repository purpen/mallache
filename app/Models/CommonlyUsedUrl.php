<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonlyUsedUrl extends BaseModel
{
    protected $table = 'commonly_used_urls';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'summary',
        'type',
        'title',
        'url',
        'user_id',
        'status',
        'cover_id',
    ];

    // 常用网站访问修改器
    public function getTypeValueAttribute()
    {
        $type = $this->type;
        $commonlyUsedUrl = config('constant.commonlyUsedUrl_type');
        if (array_key_exists($type, $commonlyUsedUrl)) {
            return $commonlyUsedUrl[$type];
        }

        return null;
    }

    /**
     * 常用网站封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage($this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 18, 1);
    }
}
