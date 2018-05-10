<?php

namespace App\Models;


class DesignNotice extends BaseModel
{
    protected $table = 'design_notice';

    // 相对关联operation_log 操作日志
    public function operationLog()
    {
        return $this->belongsTo('App\Models\OperationLog', 'operation_log_id');
    }

    /**
     * @param int $user_id 用户ID
     * @param int $operation_log_id 设计工具动态ID
     * @return DesignNotice
     */
    public static function createNotice(int $user_id, int $operation_log_id)
    {
        $design_notice = new DesignNotice();
        $design_notice->user_id = $user_id;
        $design_notice->is_read = 0;
        $design_notice->operation_log_id = $operation_log_id;
        $design_notice->save();

        if ($user = User::find($user_id)) {
            $user->increment('design_notice_count');
        }

        return $design_notice;
    }

    // 设计工作通知详情
    public function info()
    {
        return [
            'id' => $this->id,
            'is_read' => $this->is_read,
            'user_id' => $this->user_id,
            'operation_log_id' => $this->operation_log_id,
            'title' => $this->operationLog->info()['title'],  // 消息标题
            'operation_log' => $this->operationLog->info(),
        ];
    }

}
