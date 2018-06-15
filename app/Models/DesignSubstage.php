<?php

namespace App\Models;


class DesignSubstage extends BaseModel
{
    protected $table = 'design_substage';

    protected $fillable = ['execute_user_id', 'name', 'duration', 'start_time', 'summary' , 'type'];

    // 相对关联设计阶段表
    public function designStage()
    {
        return $this->belongsTo('App\Models\DesignStage', 'design_stage_id');
    }

    // 一对一关联阶段节点
    public function designStageNode()
    {
        return $this->hasOne('App\Models\DesignStageNode', 'design_substage_id');
    }

    // 一对多相对关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'execute_user_id');
    }

    public function info()
    {
        return [
            'id' => intval($this->id),
            'design_project_id' => intval($this->design_project_id),
            'design_stage_id' => intval($this->design_stage_id),
            'name' => $this->name,
            'execute_user_id' => intval($this->execute_user_id),
            'execute_user' => $this->user ?? null,
            'duration' => intval($this->duration),
            'start_time' => intval($this->start_time),
            'summary' => $this->summary,
            'user_id' => intval($this->user_id),
            'status' => intval($this->status),
            'sub_stage_image' => $this->sub_stage_image,
            'type' => intval($this->type),
            'design_stage_node' => $this->designStageNode ? $this->designStageNode->info() : null,
        ];
    }

    /**
     * 删除子阶段及其节点
     */
    public function deleteSubstage()
    {
        if ($this->designStageNode) {
            $this->designStageNode->delete();
        }

        $this->delete();
    }

    /**
     * 子阶段/里程碑附件
     */
    public function getSubStageImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 34, 1);
    }

}
