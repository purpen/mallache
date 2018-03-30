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

    public function info()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'url_code' => $this->url_code,
            'password' => $this->password,
            'share_time' => $this->share_time,
        ];
    }

}
