<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUser extends BaseModel
{
    protected $table = 'item_users';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = ['user_id', 'item_id', 'status', 'level', 'is_creator', 'type'];

    //检查是否有权限查看任务
    public static function checkUser($item_id, $user_id)
    {
        $itemUser = ItemUser::where('item_id', $item_id)->where('user_id', $user_id)->first();
        if ($itemUser) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取用户所有项目ID
     *
     * @param int $user_id
     * @return array
     */
    public static function projectId(int $user_id)
    {
        return ItemUser::where('user_id', $user_id)->get()->pluck('item_id')->all();
    }


}
