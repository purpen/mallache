<?php

namespace App\Models;

class ItemStage extends BaseModel
{
    protected $table = 'item_stage';

    protected $appends = ['item_stage_image'];
    /**
     * 允许批量赋值属性
     */
    protected $fillable = ['item_id', 'design_company_id', 'title', 'content', 'summary', 'percentage', 'amount', 'time', 'sort'];

    // 一对多相对关联需求项目表
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    /**
     * 获取图片附件
     *
     * @return array
     */
    public function getItemStageImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 8, 1, 5);
    }

    /**
     * 更改发布状态
     */
    static public function status($id, $status = 1)
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
        if ($this->content) {
            return (array)explode('&', $this->content);
        }

        return [];
    }

    public function info()
    {
        return [
            'id' => intval($this->id),
            'item_id' => intval($this->item_id),
            'design_company_id' => intval($this->design_company_id),
            'title' => strval($this->title),
            'content' => $this->array_content,
            'summary' => strval($this->summary),
            'item_stage_image' => $this->item_stage_image,
            'status' => intval($this->status),
            'created_at' => $this->created_at,
            'percentage' => $this->percentage,
            'amount' => $this->amount,
            'time' => $this->time,
            'confirm' => $this->confirm,
            'sort' => $this->sort,
        ];
    }
}
