<?php

namespace App\Listeners;

use App\Events\ItemStatusEvent;
use App\Events\PayOrderEvent;
use App\Models\FundLog;
use App\Models\Item;
use App\Models\ItemStage;
use App\Models\User;
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
     * @param  PayOrderEvent $event
     * @return void
     */
    public function handle(PayOrderEvent $event)
    {
        $pay_order = $event->pay_order;

        //需求用户账号总金额、冻结金额增加
        $user = new User();
        $user->totalAndFrozenIncrease($pay_order->user_id, $pay_order->amount);

        $fund_log = new FundLog();

        /**
         * 判断付款类型,修改对应项目付款状态
         */
        switch ($pay_order->type) {
            //项目押金
            /* case 1:
                 //资金流水记录
                 $fund_log->inFund($pay_order->user_id, $pay_order->amount, $pay_order->pay_type, $pay_order->uid,'创建项目押金');
                 //创建需求项目
                 if($item = $this->createItem($pay_order->user_id)){
                    $pay_order->item_id = $item->id;
                    $pay_order->save();
                 }
                 break;*/
            //项目尾款
            /*case 2:
                $item =Item::find($pay_order->item_id);

                //修改项目状态为项目款已托管
                $item->status = 9;
                $item->rest_fund = $item->price;
                $item->save();
                event(new ItemStatusEvent($item));

                $item_info = $item->itemInfo();
                //资金流水记录
                $fund_log->inFund($pay_order->user_id, $pay_order->amount, $pay_order->pay_type, $pay_order->uid,'【' . $item_info['name'] . '】项目款托管');
                break;*/

            // 项目首付款
            case 3:
                $item = Item::find($pay_order->item_id);

                //修改项目状态为项目首付款已托管
                $item->status = 9;
                $item->rest_fund = $pay_order->amount; // 项目当前金额
                $item->save();
                event(new ItemStatusEvent($item));

                $item_info = $item->itemInfo();
                //资金流水记录
                $fund_log->inFund($pay_order->user_id, $pay_order->amount, $pay_order->pay_type, $pay_order->uid, '【' . $item_info['name'] . '】项目首付款托管');
                break;

            // 项目阶段付款
            case 4:
                $item = Item::find($pay_order->item_id);
                $item->rest_fund = bcadd($item->rest_fund, $pay_order->amount, 2); // 增加项目当前余额
                $item->save();

                // 阶段金额已支付
                $item_stage = ItemStage::find($pay_order->item_stage_id);
                $item_stage->pay_status = 1;
                $item_stage->save();

                $item_info = $item->itemInfo();
                //资金流水记录
                $fund_log->inFund($pay_order->user_id, $pay_order->amount, $pay_order->pay_type, $pay_order->uid, '【' . $item_info['name'] . '】项目阶段款托管');
                break;
            default:
        }

    }

}
