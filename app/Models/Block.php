<?php

namespace App\Models;


class Block extends BaseModel
{
    protected $table = 'block';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'user_id',
        'name',
        'mark',
        'type',
        'code',
        'content',
        'summary',
        'count',
        'status',
    ];

}
