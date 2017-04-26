<?php

namespace App\Listeners;

use App\Events\PayOrderEvent;
use App\Models\Item;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 支付单 监听器
 * Class PayOrderListener
 * @package App\Listeners
 */
class PayOrderListener
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
     * @param  PayOrderEvent  $event
     * @return void
     */
    public function handle(PayOrderEvent $event)
    {
        $pay_order = $event->pay_order;

        /**
         * 判断付款类型,修改对应项目付款状态
         */
        switch ($pay_order->type){
            //项目押金
            case 1:
                //创建需求项目
                $item = new Item;
                $item->createItem($pay_order->user_id);
                break;
            //项目尾款
            case 2:

                break;
            default:

        }

    }
}
