<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;

class Group extends BaseModel
{
    protected $table = 'group';

    /**
     * 创建用户组
     *
     * @param string $name 用户组名称
     * @param array $user_id_arr 用户ID数组
     * @param int $type 群组类型：1.系统创建 2. 用户创建
     * @param int $company_id 公司ID
     * @return Group
     */
    public static function createGroup(string $name, array $user_id_arr, int $type = 2, int $company_id)
    {
        $group = new Group();
        $group->name = $name;
        $group->user_id_arr = json_encode($user_id_arr);
        $group->type = $type;
        $group->company_id = $company_id;
        $group->save();

        return $group;
    }


    /**
     * 向用户组添加用户
     *
     * @param array $user_id_arr
     * @return bool
     */
    public function addUser(array $user_id_arr)
    {
        $old = json_decode($this->user_id_arr, true);
        $this->user_id_arr = json_encode(array_merge($old, $user_id_arr));
        $this->save();

        return true;
    }


    /**
     * 将用户移除群组
     *
     * @param array $user_id_arr
     * @return bool
     */
    public function removeUser(array $user_id_arr)
    {
        $old = json_decode($this->user_id_arr, true);
        $new = array_diff($old, $user_id_arr);
        $this->user_id_arr = json_encode($new);
        $this->save();

        return true;
    }


    /**
     * 删除群组
     *
     * @param int $group_id
     * @param int $company_id
     * @return bool
     */
    public function deleteGroup()
    {
        // 是否有文件使用
        $dir = PanDirector::where('group_id', $this->group_id)->first();
        if ($dir) {
            return false;
        }
        $this->delete();

        return true;
    }


    /**
     * 判断用户是否在群组
     * @param int $user_id
     * @param int $group_id
     * @return bool
     */
    public static function inGroup(int $user_id, int $group_id)
    {
        $group = DB::select("select json_search(user_id_arr,'one',$user_id) from `group` where id=$group_id");
        if (!$group) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取某用户的群组列表
     *
     * @param int $user_id
     * @return mixed
     */
    public static function userGroupIDList(int $user_id)
    {
        $list = DB::select("select id from `group` WHERE json_search(user_id_arr,'one',$user_id) is not null");
        $list = collect($list);
        return $list->pluck('id')->all();
    }

    /**
     * 获取公司所有自己创建的群组列表
     *
     * @param int $company_id
     * @return mixed
     */
    public static function groupList(int $company_id)
    {
        $list = Group::select('id', 'name', 'type')
            ->where('company_id', $company_id)
            ->where('type', 2)->get();
        return $list;
    }

    /**
     *  获取一个属于公司的群组
     *
     * @param int $group_id
     * @param int $company_id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getGroup(int $group_id, int $company_id)
    {
        $group = Group::where(['id' => $group_id, 'company_id' => $company_id])->first();

        if ($group) {
            return $group;
        } else {
            return null;
        }
    }


    /**
     * 判断群组是否属于用户创建
     *
     * @return bool
     */
    public function isUserCreate()
    {
        if ($this->type == 2) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取一个群组的成员信息
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function userList()
    {
        $user_id_arr = json_decode($this->user_id_arr, true);
        $list = User::select('id', 'username')->whereIn('id', $user_id_arr)->get();
        return $list;
    }

}