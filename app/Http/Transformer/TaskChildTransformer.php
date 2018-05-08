<?php

namespace App\Http\Transformer;

use App\Models\Task;
use League\Fractal\TransformerAbstract;

class TaskChildTransformer extends TransformerAbstract
{
    /*
    id	                int(10)	否
    name	            varchar(100)	是		名称
    summary	            varchar(1000)	否		备注
    user_id	            int(11)	是		创建人ID
    execute_user_id	    int(11)	否		执行人ID
    item_id	            int(11)	是		所属项目ID
    tags	            varchar(500)	否		标签
    level	            tinyint(1)	是	1	优先级：1.普通；5.紧级；8.非常紧级；
    type	            tinyint(1)	是	1	类型;1.默认；
    sub_count	        int(11)	否	0	子项目数量
    sub_finfished_count	int(11)	否	0	子项目完成数量
    love_count	        int(11)	否	0	点赞数量
    collection_count	int(11)	否	0	收藏数量
    stage	            tinyint(1)	是	0	是否完成:0.未完成；1.进行中；2.已完成；
    start_time	        datetime	否		开始时间
    over_time	        datetime	否		完成时间
    status	            tinyint(1)	是	1	状态：0.禁用；1.启用；
    tier	            integer	            层级 默认0
    pid	                integer	            父id 默认0
    */

    public function transform(Task $tasks)
    {
        return [
            'id' => intval($tasks->id),
            'name' => strval($tasks->name),
            'tags' => strval($tasks->tags) ? explode(',' , $tasks->tags) : [],
            'summary' => $tasks->summary,
            'user_id' => intval($tasks->user_id),
            'execute_user_id' => intval($tasks->execute_user_id),
            'item_id' => intval($tasks->item_id),
            'level' => intval($tasks->level),
            'type' => intval($tasks->type),
            'sub_count' => intval($tasks->sub_count),
            'sub_finfished_count' => intval($tasks->sub_finfished_count),
            'love_count' => intval($tasks->love_count),
            'collection_count' => intval($tasks->collection_count),
            'stage' => intval($tasks->stage),
            'status' => intval($tasks->status),
            'start_time' => $tasks->start_time,
            'over_time' => $tasks->over_time,
            'created_at' => $tasks->created_at,
            'tier' => intval($tasks->tier),
            'pid' => intval($tasks->pid),
            'stage_id' => intval($tasks->stage_id),
            'childTask' => $tasks->child,
        ];
    }
}
