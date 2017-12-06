<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateOfAward extends BaseModel
{
    protected $table = 'date_of_awards';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'start_time',
        'end_time',
        'summary',
        'type',
        'name',

    ];

    // 栏目位名称访问修改器
    public function getTypeValueAttribute()
    {
        $type = $this->type;
        $dateOfAward = config('constant.dateOfAward_type');
        if (array_key_exists($type, $dateOfAward)) {
            return $dateOfAward[$type];
        }

        return null;
    }
}
