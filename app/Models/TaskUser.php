<?php

namespace App\Models;

class TaskUser extends BaseModel
{
    protected $table = 'task_users';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'selected_user_id',
        'type',
        'status',
    ];

    /**
     * 相对关联任务成员表
     */
    public function task()
    {
        return $this->belongsTo('App\Models\Task', 'task_id');
    }

    //使用任务ID获取参与人员
    public static function getTaskUser(int $task_id)
    {
        $user_id_arr = TaskUser::select('selected_user_id')
            ->where('task_id', $task_id)
            ->get()->pluck('selected_user_id')->all();

        return $user_id_arr;
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'selected_user_id');
    }
}
