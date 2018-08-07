<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends BaseModel
{
    protected $table = 'notifications';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'inform_time',
        'target_id',
        'status',
        'type',
    ];}
