<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;

class OperationLog extends BaseModel
{
    protected $table = 'operation_log';

    protected $action_type_config = [
        'task' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],  // 任务--动作类型
    ];

    // 操作动态文字说明
    protected $title_config = [
        '1' => ' 创建了任务',  // 创建任务
        '2' => ' 创建了子任务',  // 创建子任务
        '3' => ' 更改任务名称',  // 更改任务名称
        '4' => ' 更改任务备注',  // 更改任务备注
        '5' => ' 更改任务优先级',  // 更改任务优先级
        '6' => ' 重做了任务',  // 父任务重做
        '7' => ' 完成了任务',  // 父任务完成
        '8' => ' 重做了子任务',  // 子任务重做
        '9' => ' 完成了子任务',  // 子任务完成
        '10' => ' 更新了截至时间',  // 更新了截至时间
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

    //关联任务id
    public function task()
    {
        return $this->belongsTo('App\Models\Task', 'target_id');
    }

    // 一对多关联设计通知表
    public function designNotice()
    {
        return $this->hasMany('App\Models\DesignNotice', 'operation_log_id');
    }

    public static function boot()
    {
        parent::boot();

        //监听 操作动态创建
        OperationLog::created(function (OperationLog $operation_log) {

            $user_id_arr = [];
            switch ($operation_log->target_type) {
                // 任务相关的消息处理
                case 1:
                    // 查看 任务的相关人员
                    $user_id_arr = TaskUser::getTaskUser($operation_log->target_id);
                    break;
            }

            // 推送消息
            foreach ($user_id_arr as $user_id) {

                // 不需要给动态操作用户发送提醒
                if ($operation_log->user_id == $user_id) {
                    continue;
                }
                DesignNotice::createNotice($user_id, $operation_log->id);
            }
        });
    }

    /**
     * 创建动态
     *
     * @param int $company_id 公司ID
     * @param int $type 模块类型 1.项目 2.网盘
     * @param int $model_id 模块ID （与type配合使用）
     * @param int $action_type 动作类型
     * @param int $target_id 目标ID（与target_type配合使用）
     * @param int $target_type 目标类型 1.任务
     * @param int $user_id 操作人ID
     * @param int|null $other_user_id 被操作人ID
     * @param string|null $content 变更内容
     * @return OperationLog
     */
    public static function createLog(int $company_id, int $type, int $model_id, int $action_type, int $target_type, int $target_id, int $user_id, int $other_user_id = null, string $content = null)
    {
        $operation_log = new OperationLog();
        $operation_log->company_id = $company_id;
        $operation_log->type = $type;
        $operation_log->model_id = $model_id;
        $operation_log->action_type = $action_type;
        $operation_log->target_type = $target_type;
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
                $str = $this->updateTaskName();
                break;
            case 4:
                $str = $this->updateTaskSummary();
                break;
            case 5:
                $str = $this->updateTaskLevel();
                break;
            case 6:
                $str = $this->noStage();
                break;
            case 7:
                $str = $this->isStage();
                break;
            case 8:
                $str = $this->noChildStage();
                break;
            case 9:
                $str = $this->isChildStage();
                break;
            case 10:
                $str = $this->updateOverTime();
                break;
        }

        return [
            'action_type' => $this->action_type,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
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
        $logs = OperationLog::where('target_type', 1)
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

    // 更改任务名称
    public function updateTaskName()
    {
        return $this->user->getUserName() . $this->title_config['3'] . $this->task->getTaskName();
    }

    // 更改任务备注
    public function updateTaskSummary()
    {
        return $this->user->getUserName() . $this->title_config['4'] . $this->task->getTaskSummary();
    }

    // 更改任务优先级
    public function updateTaskLevel()
    {
        return $this->user->getUserName() . $this->title_config['5'] . $this->task->getTaskLevel();
    }

    //父任务重做
    public function noStage()
    {
        return $this->user->getUserName() . $this->title_config['6'];

    }

    //父任务完成
    public function isStage()
    {
        return $this->user->getUserName() . $this->title_config['7'];

    }

    //子任务重做
    public function noChildStage()
    {
        return $this->user->getUserName() . $this->title_config['8'] . $this->task->getTaskName();

    }

    //子任务完成
    public function isChildStage()
    {
        return $this->user->getUserName() . $this->title_config['9'] . $this->task->getTaskName();

    }

    // 更改任务备注
    public function updateOverTime()
    {
        return $this->user->getUserName() . $this->title_config['10'] . $this->task->getOverTime();
    }

}