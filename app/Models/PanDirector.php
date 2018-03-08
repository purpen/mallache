<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanDirector extends Model
{
    protected $table = 'pan_director';

    /**
     * 一对多相对关联源文件表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function panFile()
    {
        return $this->belongsTo('App\Models\PanFile', 'pan_file_id');
    }
}
