<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 系统通知
 */
class Notice extends BaseModel
{
    protected $table = 'notice';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'title',
        'user_id',
        'cover_id',
        'type',
        'evt',
        'summary',
        'content',
        'url',
      ];

    /**
     * 封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage((int)$this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 28, 1);
    }
}
