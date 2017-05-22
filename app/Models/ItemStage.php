<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemStage extends BaseModel
{
    protected $table = 'item_stage';

    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['item_id', 'design_company_id' , 'title' , 'content' , 'summary' , 'status'];


    /**
     * 获取图片附件
     *
     * @return array
     */
    public function getItemStageImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 8, 1 , 5);
    }

    /**
     * 更改发布状态
     */
    static public function status($id, $status=1)
    {
        $itemStage = self::findOrFail($id);
        $itemStage->status = $status;
        return $itemStage->save();
    }
}
