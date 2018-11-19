<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends BaseModel
{
    protected $table = 'statistics';

    /**
     * 可被批量赋值的字段
     * @var array
     */
    protected $fillable = [
        'tourist_count',
        'willing_count',
    ];}
