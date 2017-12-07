<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Works extends BaseModel
{
    /**
     * 关联模型到数据表
     * @var string
     */
    protected $table = 'works';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['kind', 'title', 'content', 'company_id', 'user_id', 'summary', 'status', 'cover_id', 'published', 'view_count', 'match_id', 'tags'];


    /**
     * 案例图片
     */
    public function getImagesAttribute()
    {
        return AssetModel::getImageUrl($this->id, 15, 1);
    }

    /**
     * 封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage((int)$this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 15, 1);
    }

    /**
     * 相对关联设计公司表
     */
    public function company()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'company_id');
    }
}
