<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class CollectItem extends BaseModel
{
    protected $table = 'collect_item';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'collect',
        'type',
        'status',
    ];

    /**
     * 获取用户所有项目ID
     *
     * @param int $user_id 用户ID
     * @param int $status 状态：1.正常 2.回收站
     * @return array
     */
    public static function collectId(int $user_id, $status = 1 ,$collect)
    {

        if($collect == 1){
            return DB::table('collect_item')
                ->select('design_project.id as id')
                ->join('design_project', 'collect_item.item_id', '=', 'design_project.id')
                ->where('collect_item.user_id', $user_id)
                ->where('collect_item.collect', 1)
                ->where('design_project.status', $status)
                ->get()->pluck('id')->all();
        }else{
            return DB::table('collect_item')
                ->select('design_project.id as id')
                ->join('design_project', 'collect_item.item_id', '=', 'design_project.id')
                ->where('collect_item.user_id', $user_id)
                ->where('design_project.status', $status)
                ->get()->pluck('id')->all();
        }

    }

}
