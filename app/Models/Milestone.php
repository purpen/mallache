<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Milestone extends BaseModel
{
    protected $table = 'milestones';

    protected $fillable = ['design_project_id' , 'design_stage_id' , 'name', 'start_time', 'summary'];


    // 相对关联设计阶段表
    public function designStage()
    {
        return $this->belongsTo('App\Models\DesignStage', 'design_stage_id');
    }

    public function info()
    {
        return [
            'id' => intval($this->id),
            'design_project_id' => intval($this->design_project_id),
            'design_stage_id' => intval($this->design_stage_id),
            'name' => $this->name,
            'start_time' => intval($this->start_time),
            'summary' => $this->summary,
            'user_id' => intval($this->user_id),
            'status' => intval($this->status),
            'milestone_image' => $this->milestone_image,
        ];
    }
    /**
     * 里程碑附件
     */
    public function getMilestoneImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 36, 1);
    }
}
