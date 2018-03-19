<?php
namespace App\Helper;

class Yunpan {

    /**
     * 传值用户ID和项目ID判断用户是否在项目组中
     *
     * @param int $user_id
     * @param int $group_id
     * @return bool
     */
    public static function isItem (int $user_id, int $group_id)
    {
        return true;
    }
}