<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 消息模型
 * Class Message
 * @package App\Models
 */
class Message extends BaseModel
{
    protected $table = 'message';

    protected $fillable = ['user_id', 'content', 'type', 'title', 'target_id'];

}
