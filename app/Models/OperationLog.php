<?php

namespace App\Models;

class OperationLog extends BaseModel
{
    protected $table = 'operation_log';

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

}