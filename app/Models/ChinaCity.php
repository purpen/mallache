<?php
/**
 * 城市表(收货地址－－京东)
 */

namespace App\Models;

class ChinaCity extends BaseModel
{
    public $timestamps = false;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'china_cities';

    /**
     * 获取关联信息
     */
    static public function fetchCity($pid=0)
    {
        $query['pid'] = (int)$pid;
        $query['status'] = 1;

        $cities = self::where($query)->orderBy('sort', 'desc')->get();
        return $cities;
    }

}