<?php

namespace App\Listeners;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use App\Models\DesignCompanyModel;
use App\Models\Item;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ItemMessageListener
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
     * @param  ItemStatusEvent  $event
     * @return void
     */
    public function handle(ItemStatusEvent $event)
    {
        $item = $event->item;

        switch ($item->status){
            //已推荐设计公司
            case 3:
                $this->recommendDesign($item);
                break;
            //向设计公司推送项目
            case 4:
                $this->pushItemToDesign($event);
                break;
            //选定设计公司
            case 5:

        }
    }

    /**
     * 已为项目匹配了设计公司
     *
     * @param Item $item
     */
    public function recommendDesign(Item $item)
    {
        $item_info = $item->itemInfo();
        //添加系统通知
        $tools = new Tools();
        $tools->message($item->user_id, '【' . $item_info['name'] . '】' . '已匹配了合适的设计公司');
    }

    /**
     * 向设计公司推送项目
     */
    public function pushItemToDesign(ItemStatusEvent $event)
    {
        $item = $event->item;
        $design_company_id = $event->design_company_id;

//        DesignCompanyModel::whereIn()

        //添加系统通知
        $tools = new Tools();
        $n = count($design_company_id);
        for ($i = 0; $i < $n; ++$i){
            if(!$design_company = DesignCompanyModel::find($design_company_id[$i])){
                continue;
            }
            $tools->message($design_company->user_id, '系统向您推荐了项目' . '【' . $item->itemInfo()['name'] . '】');
        }
    }

    /**
     * 用户确认报价单选定设计公司、向选中设计公司和落选的设计公司发送系统消息
     */
    public function trueDesign(ItemStatusEvent $event)
    {
        $item = $event->item;
        //添加系统通知
        $tools = new Tools();
        $tools->message($item->, '【' . $item_info['name'] . '】' . '已匹配了合适的设计公司');
    }

}
