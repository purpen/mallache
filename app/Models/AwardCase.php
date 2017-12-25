<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardCase extends BaseModel
{
    protected $table = 'award_case';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'title',
        'user_id',
        'cover_id',
        'images_url',
        'type',
        'category_id',
        'grade',
        'tags',
        'summary',
        'content',
        'url',
        'recommended',
        'recommended_on',
        'time_at',
      ];

    // 奖项分类访问修改器
    public function getCategoryValueAttribute()
    {
        $category_id = $this->category_id;
        $awardCase = config('constant.awardCase_category');
        if (array_key_exists($category_id, $awardCase)) {
            return $awardCase[$category_id];
        }

        return null;
    }

    /**
     * tags 标签访问修改器
     * @return array
     */
    public function getTagsAttribute($key)
    {
        return $key ? explode(',',$key) : [];
    }


    /**
     * 图片列表
     */
    public function getImagesAttribute()
    {
        return AssetModel::getImageUrl($this->id, 25, 1);
    }

    /**
     * 封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage((int)$this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 25, 1);
    }
}
