<?php

namespace App\Http\Transformer;

use App\Models\Stage;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class StageTransformer extends TransformerAbstract
{
    /*
id	int(10)	否
item_id	int(11)	是		项目ID
title	varchar(100)	是		标题
    */

    public function transform(Stage $stage)
    {
        if($stage->stage !== 0){
            if($stage->stage == -1){
                $stage->stage = 0;
            }
            $tasks= Task::where(['item_id' => (int)$stage->item_id , 'stage_id' =>  $stage->id, 'status' => 1 , 'stage' => $stage->stage ])->get();
        }else{
            $tasks= Task::where(['item_id' => (int)$stage->item_id , 'stage_id' =>  $stage->id, 'status' => 1])->get();
        }
        foreach ($tasks as $task){
            $user_id = $task->execute_user_id;
            $user = User::find($user_id);
            if($user){
                $task['logo_image'] = $user->logo_image;
            }else{
                $task['logo_image'] = '';
            }
        }
        return [
            'id' => intval($stage->id),
            'item_id' => intval($stage->item_id),
            'title' => $stage->title,
            'created_at' => $stage->created_at,
            'task' => $tasks ? $tasks : '',
        ];
    }
}
