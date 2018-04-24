<?php

namespace App\Models;


class DesignSubstage extends BaseModel
{
    protected $table = 'design_substage';

    protected $fillable = ['execute_user_id', 'name', 'duration', 'start_time', 'summary'];

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
            'id' => $this->id,
            'design_project_id' => $this->design_project_id,
            'design_stage_id' => $this->design_stage_id,
            'name' => $this->name,
            'execute_user_id' => $this->execute_user_id,
            'execute_user' => $this->user ?? null,
            'duration' => $this->duration,
            'start_time' => $this->start_time,
            'summary' => $this->summary,
            'user_id' => $this->user_id,
            'status' => $this->status,
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

}
