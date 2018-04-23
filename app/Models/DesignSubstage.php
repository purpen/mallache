<?php

namespace App\Models;


class DesignSubstage extends BaseModel
{
    protected $table = 'design_substage';

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

    public function info()
    {
        return [
            'id' => $this->id,
            'design_project_id' => $this->design_project_id,
            'design_stage_id' => $this->design_stage_id,
            'name' => $this->name,
            'execute_user_id' => $this->execute_user_id,
            'duration' => $this->duration,
            'start_time' => $this->start_time,
            'summary' => $this->summary,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'design_stage_node' => $this->designStageNode ? $this->designStageNode->info() : null,
        ];
    }
}
