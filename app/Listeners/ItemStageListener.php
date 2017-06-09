<?php

namespace App\Listeners;

use App\Events\ItemStageEvent;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ItemStageListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ItemStageEvent  $event
     * @return void
     */
    public function handle(ItemStageEvent $event)
    {
       //
    }

}
