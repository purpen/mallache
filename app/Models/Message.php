<?php

namespace App\Models;

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
