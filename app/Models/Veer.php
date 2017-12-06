<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veer extends BaseModel
{
    protected $table = 'veers';

    /**
     * 允许批量地址
     */
    protected $fillable = [
        'client_id',
        'access_token',
        'refresh_token'
    ];}
