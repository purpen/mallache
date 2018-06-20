<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

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
     * @param int $user_id 用户ID
     * @param int $status 状态：1.正常 2.回收站
     * @return array
     */
    public static function projectId(int $user_id, $status = 1)
    {
//        return ItemUser::where('user_id', $user_id)->get()->pluck('item_id')->all();

        return DB::table('item_users')
            ->select('design_project.id as id')
            ->join('design_project', 'item_users.item_id', '=', 'design_project.id')
            ->where('item_users.user_id', $user_id)
            ->where('design_project.status', $status)
            ->get()->pluck('id')->all();
    }

    /**
     * 添加项目成员
     *
     * @param int $item_id 项目ID
     * @param int $user_id 用户ID
     * @param int $is_creator 是否是创建者: 0.否；1.是；
     * @return bool
     */
    public static function addItemUser(int $item_id, int $user_id, int $is_creator = 0)
    {
        $item_user = new ItemUser();
        $item_user->item_id = $item_id;
        $item_user->user_id = $user_id;
        $item_user->is_creator = $is_creator;
        return $item_user->save();
    }

    /**
     * 修改项目成员级别
     *
     * @param int $level 级别：3.项目负责人；5.商务负责人；
     * @return bool
     */
    public function changeLevel(int $level)
    {
        if (!in_array($level, [3, 5])) {
            return false;
        }

        // 项目负责人、商务负责人 分别只能有一个,清除之前的
        $item_users = ItemUser::where(['item_id' => $this->item_id, 'level' => $level])->get();
        foreach ($item_users as $item_user) {
            $item_user->level = 1;
            $item_user->save();
        }

        $this->level = $level;
        return $this->save();
    }

    // 获取项目用户对象
    public static function getItemUser($item_id, $user_id)
    {
        $itemUser = ItemUser::where('item_id', $item_id)
            ->where('user_id', $user_id)
            ->where('status', 1)
            ->first();

        return $itemUser;
    }

    //通知人员
    public static function getItemUserArr(int $item_user_id)
    {
        $user_id_arr = ItemUser::select('user_id')
            ->where('item_id', $item_user_id)
            ->get()->pluck('user_id')->all();

        return $user_id_arr;
    }


}
