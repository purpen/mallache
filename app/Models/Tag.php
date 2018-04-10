<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends BaseModel
{
    protected $table = 'tags';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'type',
        'title',
        'task_id',
    ];

    /**
     * 返回
     */
    protected $appends = [
        'type_val',

    ];

    //根据标签id，查看任务里面含有该任务的任务task_id_array
    public static function tagTask($id)
    {
        //获取所有任务
        $tasks = Task::get();
        $task_id_arr = [];
        foreach ($tasks as $task){
            //获取单个标签
            $tags = $task->tags;
            if(!empty($tags)){
                $new_tags = explode(',' , $tags);
                //如果传来的标签id在任务标签中，记录下来
                if(in_array($id , $new_tags)){
                    //把有关任务的标签存起来
                    $task_id_arr[] = $task->id;
                }
            }
        }
        $new_task_id_arr = $task_id_arr;

        return $new_task_id_arr;
    }

    //颜色类型
    public function getTypeValAttribute()
    {
        if(array_key_exists($this->type,config('constant.tag_type'))){
            return config('constant.tag_type')[$this->type];

        }
        return '';
    }
}
