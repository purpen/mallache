<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UDesign extends Model
{
    protected $table = 'u_design';

    //允许批量赋值的属性
    protected $fillable = [
        'system',
        'design_content',
        'page_number',
        'name',
        'stage',
        'complete_content',
        'other_content',
        'style',
        'start_time',
        'cycle',
        'design_cost',
        'province',
        'city',
        'summary',
        'artificial',
    ];

}
