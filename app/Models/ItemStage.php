<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemStage extends Model
{
    protected $table = 'item_stage';

    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['item_id', 'design_company_id' , 'title' , 'content' , 'summary'];


    /**
     * 获取图片附件
     *
     * @return array
     */
    public function getItemStageImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 8, 1 , 5);
    }
}
