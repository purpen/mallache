<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Task extends BaseModel
{
    protected $table = 'tasks';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['name', 'summary', 'user_id', 'execute_user_id', 'item_id', 'tags', 'level', 'type', 'sub_count', 'sub_finfished_count', 'love_count', 'collection_count' , 'stage' , 'start_time' , 'over_time' , 'status'];


    /**
     * 一对多子任务
     */
    public function subTask()
    {
        return $this->hasMany('App\Models\SubTask' , 'task_id');
    }

    /**
     * 一对多任务成员表
     */
    public function taskUser()
    {
        return $this->hasMany('App\Models\SubTask' , 'task_id');
    }

    /**
     * 任务完成与未完成
     */
    public static function isStage(int $task_id  , int $stage)
    {
        $task = self::find($task_id);
        $task->stage = $stage;
        $task->save();

        return true;
    }
}

