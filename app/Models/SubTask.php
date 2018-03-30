<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTask extends BaseModel
{
    protected $table = 'sub_tasks';

    /**
     * 相对关联任务表
     */
    public function task()
    {
        return $this->belongsTo('App\Models\Task' , 'task_id');
    }
}
