<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends BaseModel
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'article';

    protected $fillable = ['title', 'content', 'classification_id', 'cover_id', 'type', 'topic_url', 'label', 'short_content', 'source_from'];

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

    /**
     * label 标签访问修改器
     * @return array
     */
    public function getLabelAttribute($key)
    {
        return $key ? explode(',',$key) : [];
    }

}