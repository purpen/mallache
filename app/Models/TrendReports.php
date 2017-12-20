<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendReports extends BaseModel
{
    /**
     * 关联模型到数据表
     * @var string
     */
    protected $table = 'trend_reports';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['title', 'hits' ,'cover_id' , 'status' , 'pdf_id' , 'summary' , 'tag' , 'user_id'];

    /**
     * 趋势报告封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage($this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 16, 1);
    }

    /**
     * 趋势报告pdf
     */
    public function getImageAttribute()
    {
        return AssetModel::getOneImage($this->pdf_id) ?? AssetModel::getOneImageUrl($this->id, 17, 1);
    }
}
