<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommuneSummary extends BaseModel
{
    protected $table = 'commune_summaries';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'status',
        'title',
        'content',
        'location',
        'expire_time',
        'other_realname',
    ];

    /**
     * 案例图片
     */
    public function getCommuneImageAttribute()
    {
        return AssetModel::getImageUrl($this->id, 29, 1);
    }

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    //获取沟通纪要相关人员
    public static function getCommuneSummaryUserArr(int $commune_summary_id)
    {
        $user_id_arr = CommuneSummaryUser::select('selected_user_id')
            ->where('id', $commune_summary_id)
            ->get()->pluck('selected_user_id')->all();

        return $user_id_arr;
    }
}
