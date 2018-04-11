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
    ];
}
