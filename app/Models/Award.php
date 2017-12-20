<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends BaseModel
{
    protected $table = 'award';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'title',
        'user_id',
        'cover_id',
        'images',
        'type',
        'kind',
        'grade',
        'content',
        'time_at',
    ];}
