<?php
namespace App\Http\Transformer;

use App\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    public function transform(Message $message)
    {
        return [
            'id' => $message->id,
            'type' => (int)$message->type,
            'content' => $message->content,
            'created_at' => $message->created_at->format('Y-m-d H:i:s'),
        ];
    }
}