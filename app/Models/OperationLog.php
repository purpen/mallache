<?php

namespace App\Models;

class OperationLog extends BaseModel
{
    protected $table = 'operation_log';

    // 操作动态文字说明
    protected $title_config = [
        '1' => ' 创建了任务',  // 创建任务
        '2' => ' 创建了子任务',  // 创建子任务
        '3' => ' 更改任务',  // 更改任务
    ];

    //关联操作用户
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    // 关联被操作用户
    public function otherUser()
    {
        return $this->belongsTo('App\Models\User', 'other_user_id');
    }

    /**
     * 创建动态
     *
     * @param int $company_id 公司ID
     * @param int $type 模块类型 1.项目 2.网盘
     * @param int $model_id 模块ID （与type配合使用）
     * @param int $action_type 动作类型
     * @param int $target_id 目标ID（与action_type配合使用）
     * @param int $user_id 操作人ID
     * @param int|null $other_user_id 被操作人ID
     * @param string|null $content 变更内容
     * @return OperationLog
     */
    public static function createLog(int $company_id, int $type, int $model_id, int $action_type, int $target_id, int $user_id, int $other_user_id = null, string $content = null)
    {
        $operation_log = new OperationLog();
        $operation_log->company_id = $company_id;
        $operation_log->type = $type;
        $operation_log->model_id = $model_id;
        $operation_log->action_type = $action_type;
        $operation_log->target_id = $target_id;
        $operation_log->user_id = $user_id;
        $operation_log->other_user_id = $other_user_id;
        $operation_log->content = $content;
        $operation_log->save();

        return $operation_log;
    }

    //动态信息
    public function info()
    {
        $str = null;
        switch ($this->action_type) {
            case 1:
                $str = $this->masterTask();
                break;
            case 2:
                $str = $this->childTask();
                break;
            case 3:
                $str = $this->updateTask();
                break;
        }

        return [
            'action_type' => $this->action_type,
            'title' => $str,
            'content' => $this->content,
            'created_at' => $this->created_at
        ];
    }

    /**
     * 获取任务动态
     *
     * @param int $task_id 任务ID
     * @return array
     */
    public static function getTaskLog(int $task_id)
    {
        // 任务动态类型
        $arr = [1, 2 , 3];
        $logs = OperationLog::whereIn('action_type', $arr)
            ->where('target_id', $task_id)->get();

        $resp_data = [];
        foreach ($logs as $obj) {
            $resp_data[] = $obj->info();
        }

        return $resp_data;
    }

    // 创建主任务
    public function masterTask()
    {
        return $this->user->getUserName() . $this->title_config['1'];
    }

    // 创建子任务
    public function childTask()
    {
        return $this->user->getUserName() . $this->title_config['2'];
    }

    // 更改任务
    public function updateTask()
    {
        return $this->user->getUserName() . $this->title_config['3'];
    }


}