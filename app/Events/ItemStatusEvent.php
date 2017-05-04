<?php

namespace App\Events;

use App\Models\Item;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ItemStatusEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    //项目实例
    public $item;

    //接收消息的设计公司ID
    public $design_company_id;

    /**
     * Create a new event instance.
     *
     * @param Item $item 项目实例
     * @param array $design_company_id 接收消息的设计公司ID 数组
     *
     * @return void
     */
    public function __construct(Item $item, array $design_company_id = [])
    {
        $this->item = $item;
        $this->design_company_id = $design_company_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
