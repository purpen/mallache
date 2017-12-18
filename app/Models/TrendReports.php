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
    protected $fillable = ['title', 'hits' ,'cover_id' , 'status' , 'pdf_id' , 'summary'];

    /**
     * 趋势报告
     */
    public function getImageAttribute()
    {
        return AssetModel::getOneImage($this->id);
    }
}
