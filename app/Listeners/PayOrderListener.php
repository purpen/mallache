<?php

namespace App\Listeners;

use App\Events\PayOrderEvent;
use App\Models\Item;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * 支付单支付成功 监听器
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
                if($item = $this->createItem($pay_order->user_id)){
                   $pay_order->item_id = $item->id;
                   $pay_order->save();
                }
                break;
            //项目尾款
            case 2:

                break;
            default:

        }

    }

    //创建需求表
    public function createItem($user_id)
    {
        $item = Item::create([
            'user_id' => $user_id,
            'status' => 1,
            'type' => 0,
            'design_type' => 0
        ]);
        if($item){
            return $item;
        }else{
            Log::error('创建需求表报错');
            return false;
        }
    }
}
