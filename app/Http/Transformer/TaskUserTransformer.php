<?php

namespace App\Http\Transformer;

use App\Models\TaskUser;
use League\Fractal\TransformerAbstract;

class TaskUserTransformer extends TransformerAbstract
{
    /*
id	int(10)	否
task_id	int(11)	是		所属任务ID
user_id	int(11)	是		所属用户ID
type	tinyint(1)	是	1	类型;1.默认；
status	tinyint(1)	是	1	状态：0.禁用；1.启用；
    */

    public function transform(TaskUser $taskUsers)
    {
        return [
            'id' => intval($taskUsers->id),
            'task_id' => intval($taskUsers->task_id),
            'user_id' => intval($taskUsers->user_id),
            'selected_user_id' => intval($taskUsers->selected_user_id),
            'type' => intval($taskUsers->type),
            'status' => intval($taskUsers->status),
            'created_at' => $taskUsers->created_at,
        ];
    }
}
