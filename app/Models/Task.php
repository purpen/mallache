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
    protected $fillable = [
        'name',
        'summary',
        'user_id',
        'execute_user_id',
        'item_id',
        'tags',
        'level',
        'type',
        'sub_count',
        'sub_finfished_count',
        'love_count',
        'collection_count' ,
        'stage' ,
        'start_time' ,
        'over_time' ,
        'status' ,
        'pid',
        'tier',
        'stage_id'
    ];


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
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
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

    /**
     * 获取任务名称名称
     *
     * @return mixed
     */
    public function getTaskName()
    {
        return $this->name ? $this->name : '';
    }

    /**
     * 获取任务备注
     *
     * @return mixed
     */
    public function getTaskSummary()
    {
        return $this->summary ? $this->summary : '';
    }

    /**
     * 获取任务优先级
     *
     * @return mixed
     */
    public function getTaskLevel()
    {
        switch ($this->level){
            case 1:
                return '普通';
                break;
            case 2:
                return '紧级';
                break;
            case 3:
                return '非常紧级';
                break;
        }
    }

    /**
     * 获取完成时间
     *
     * @return mixed
     */
    public function getOverTime()
    {
        return $this->over_time ? $this->over_time : '';
    }
}

