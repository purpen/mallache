<?php

namespace App\Models;

class Article extends BaseModel
{
    protected $table = 'article';

    protected $fillable = ['title', 'content', 'classification_id', 'cover_id'];

    /**
     * 一对多相对关分类表classification
     */
    public function classification()
    {
        return $this->belongsTo('App\Models\Classification', 'classification_id');
    }

    /**
     * 封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage((int)$this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 13, 1);
    }

}