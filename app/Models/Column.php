<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends BaseModel
{
    /**
     * 关联模型到数据表
     * @var string
     */
    protected $table = 'column';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['type', 'title', 'content', 'url', 'sort', 'status', 'cover_id' , 'facility'];

    // 栏目位名称访问修改器
    public function getTypeValueAttribute()
    {
        $type = $this->type;
        $column = config('constant.column_type');
        if (array_key_exists($type, $column)) {
            return $column[$type];
        }

        return null;
    }

    /**
     * 案例图片
     */
    public function getImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 12, 1);
    }

    /**
     * 封面图
     */
    public function getCoverAttribute()
    {
        return AssetModel::getOneImage((int)$this->cover_id) ?? AssetModel::getOneImageUrl($this->id, 12, 1);
    }
}
