<?php

namespace App\Models;

class DesignStageNode extends BaseModel
{
    protected $table = 'stage_node';

    protected $fillable = [
        'name',
        'time',
        'is_owner',
        'status'
    ];

    // 一对一相对关联设计子阶段
    public function designSubstage()
    {
        return $this->belongsTo('App\Models\DesignSubstage', 'design_substage_id');
    }

    public function info()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'time' => $this->time,
            'is_owner' => $this->is_owner,
            'design_substage_id' => $this->design_substage_id,
            'design_project_id' => $this->design_project_id,
            'status' => $this->status,
            'asset' => AssetModel::getImageUrl($this->id, 31),
        ];
    }

}
