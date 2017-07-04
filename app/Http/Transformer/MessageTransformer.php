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
            'title' => $message->title,
            'content' => $message->content,
            'target_id' => $message->target_id,
            'created_at' => $message->created_at,
            'status' => $message->status,
            'is_url' => $this->isUrl((int)$message->type),
        ];
    }

    protected function isUrl($type)
    {
        switch ($type){
            case 1:
                $val = 0;
                break;
            case 2:
                $val = 1;
                break;
            case 3:
                $val = 1;
                break;
            default:
                $val = 0;
        }

        return $val;
    }
}