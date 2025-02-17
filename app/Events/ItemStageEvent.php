<?php

namespace App\Events;

use App\Models\ItemStage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ItemStageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // 项目阶段实例
    public $itemStage;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ItemStage $itemStage)
    {
        $this->itemStage = $itemStage;
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
