<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskUser extends BaseModel
{
    protected $table = 'task_users';

    /**
     * 相对关联任务成员表
     */
    public function task()
    {
        return $this->belongsTo('App\Models\Task' , 'task_id');
    }
}
