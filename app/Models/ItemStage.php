<?php

namespace App\Models;

class ItemStage extends BaseModel
{
    protected $table = 'item_stage';

    protected $appends = ['item_stage_image'];
    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['item_id', 'design_company_id' , 'title' , 'content' , 'summary', 'percentage', 'amount', 'time', 'sort'];


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

    /**
     * 阶段内容访问修改器
     */
    public function getArrayContentAttribute()
    {
        if($this->content){
            return (array)explode('&', $this->content);
        }

        return [];
    }
}
