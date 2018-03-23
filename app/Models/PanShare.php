<?php

namespace App\Models;

class PanShare extends BaseModel
{
    protected $table = 'pan_share';

    /**
     * 一对一相对关联文件层级表
     */
    public function panDirector()
    {
        return $this->belongsTo('App\Models\PanDirector', 'pan_director_id');
    }

    /*public function info()
    {
        return [
            'id' => $this->id,
            'pan_director_id' => $this->panDirector->info(),

        ];
    }*/
}
