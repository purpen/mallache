<?php

namespace App\Models;


class DesignNotice extends BaseModel
{
    protected $table = 'design_notice';

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
}
