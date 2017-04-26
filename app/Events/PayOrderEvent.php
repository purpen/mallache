<?php

namespace App\Events;

use App\Models\PayOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


/**
 * 付款单事件
 * Class PayOrderEvent
 * @package App\Events
 */
class PayOrderEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pay_order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PayOrder $pay_order)
    {
        $this->pay_order = $pay_order;
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
