<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        //支付事件
        'App\Events\PayOrderEvent' => [
            'App\Listeners\PayOrderListener',
        ],
        //项目状态变化事件
        'App\Events\ItemStatusEvent' => [
            //添加系统通知
            'App\Listeners\ItemMessageListener',
        ],
        // 项目阶段确认事件
        'App\Events\ItemStageEvent' => [
            // 事件监听器
            'App\Listeners\ItemStageListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
