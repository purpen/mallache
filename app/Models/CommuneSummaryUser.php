<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommuneSummaryUser extends BaseModel
{
    protected $table = 'commune_summary_users';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'user_id',
        'commune_summary_id',
        'status',
        'selected_user_id',
        'type',
    ];

    /**
     * 相对关联到User用户表
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'selected_user_id');
    }
}
