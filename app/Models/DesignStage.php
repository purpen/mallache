<?php

namespace App\Models;

class DesignStage extends BaseModel
{
    protected $table = 'design_stage';

    protected $guarded = ['status', 'design_project_id'];

    protected $fillable = ['name', 'duration', 'start_time', 'content'];

    // 相对关联项目表
    public function designProject()
    {
        return $this->belongsTo('App\Models\DesignProject', 'design_project_id');
    }

    //一对多关联阶段任务表
    public function designSubstage()
    {
        return $this->hasMany('App\Models\DesignSubstage', 'design_stage_id');
    }

    public function info()
    {
        $design_substages = $this->designSubstage;
        $arr = [];
        if ($design_substages->isEmpty()) {
            $arr = null;
        } else {
            foreach ($design_substages as $value) {
                $arr[] = $value->info();
            }
        }

        return [
            'id' => intval($this->id),
            'design_project_id' => intval($this->design_project_id),
            'name' => $this->name,
            'duration' => $this->duration,
            'start_time' => intval($this->start_time),
            'content' => $this->content,
            'user_id' => intval($this->user_id),
            'status' => intval($this->status),
            'statistical' => intval($this->statistical),
            'stage_image' => $this->stage_image,
            'design_substage' => $arr,
        ];
    }

    /**
     * 删除阶段及其子阶段和节点
     */
    public function deleteStage()
    {
        $design_substages = $this->designSubstage;
        if (!$design_substages->isEmpty()) {
            foreach ($design_substages as $v) {
                $v->deleteSubstage();
            }
        }

        $this->delete();
    }

    /**
     * 阶段附件图片
     */
    public function getStageImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 35, 1);
    }

}
