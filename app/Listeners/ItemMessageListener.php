<?php

namespace App\Listeners;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use App\Models\DesignCompanyModel;
use App\Models\FundLog;
use App\Models\Item;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 项目状态事件监听器--对项目状态变更进行相关操作
 *
 * Class ItemMessageListener
 * @package App\Listeners
 */
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
            //项目匹配失败
            case -2:
                $this->itemFail($event);
                break;
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
                $this->trueDesign($event);
                break;
            //设计公司提交合同
            case 6:
                $this->designSubmitContract($event);
                break;
            //发布需求公司已确认合同，
            case 7:
                $this->demandTrueContract($event);
                break;
            //等待项目款托管
            case 8:
                break;
            //项目款已托管
            case 9:
                //向设计公司通知
                $this->demandTrustFunds($event);
                //项目向设计公司支付部分费用
                $this->payPriceToDesign($event);
                break;
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

        $user_id_arr = DesignCompanyModel::select('user_id')
            ->whereIn('id', $design_company_id)
            ->get()
            ->pluck('user_id')
            ->all();

        //添加系统通知
        $tools = new Tools();
        $n = count($user_id_arr);
        for ($i = 0; $i < $n; ++$i){
            $tools->message($user_id_arr[$i], '系统向您推荐了项目' . '【' . $item->itemInfo()['name'] . '】');
        }
    }

    /**
     * 用户确认报价单选定设计公司、向选中设计公司和落选的设计公司发送系统消息
     */
    public function trueDesign(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();
        $tools = new Tools();

        //选定公司ID
        $design_company_id = $event->design_company_id['yes'];
        $design = DesignCompanyModel::find($design_company_id);
        $tools->message($design->user_id, '【' . $item_info['name'] . '】' . '确认了您的报价');

        //拒绝公司ID
        $design_company_id_arr = $event->design_company_id['no'];
        //添加系统通知
        $designCompanies = DesignCompanyModel::whereIn('id', $design_company_id_arr)->get();

        $user_id_arr = $designCompanies->pluck('user_id')->all();
        //添加系统通知
        $n = count($user_id_arr);
        for ($i = 0; $i < $n; ++$i){
            $tools->message($user_id_arr[$i], '【' . $item_info['name'] . '】' . '已选择其他设计公司');
        }
    }

    //项目匹配失败-通知用户
    public function itemFail(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $tools = new Tools();
        $tools->message($item->user_id, '【' . $item_info['name'] . '】' . '未达成合作，匹配失败');
    }

    //设计公司提交合同，通知需求公司
    public function designSubmitContract(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $tools = new Tools();
        $tools->message($item->user_id, '【' . $item_info['name'] . '】' . '设计公司已提交合同，请查阅');
    }

    //需求公司确认合同，通知设计公司
    public function demandTrueContract(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //获取设计公司user_id
        $user_id = $item->designCompany->user_id;

        $tools = new Tools();
        $tools->message($user_id, '【' . $item_info['name'] . '】' . '需求公司已确认合同');
    }

    //需求公司已托管项目资金
    public function demandTrustFunds(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //获取设计公司user_id
        $user_id = $item->designCompany->user_id;

        //通知设计公司
        $tools = new Tools();
        $tools->message($user_id, '【' . $item_info['name'] . '】' . '需求公司已托管项目资金');
    }

    //需求公司托管资金后，首次向设计支付一定比例项目款
    public function payPriceToDesign(ItemStatusEvent $event)
    {
        $item = $event->item;

        //项目总金额
        $item_price = $item->price;
        //需要转账金额
        $amount = number_format($item_price * config('constant.first_pay'), 2, '.', '');

        DB::beginTransaction();
        $user_model = new User();

        try{
            $demand_user_id = $item->user_id;
            $item_info = $item->itemInfo();

            //修改项目剩余项目款
            $item->rest_fund -= $amount;
            $item->save();

            //减少需求公司账户金额（总金额、冻结金额）
            $user_model->totalAndFrozenDecrease($demand_user_id, $amount);

            //设计公司用户ID
            $design_user_id = $item->designCompany->user_id;
            //增加设计公司账户总金额
            $user_model->totalIncrease($design_user_id, $amount);

            $fund_log = new FundLog();
            //需求公司流水记录
            $fund_log->outFund($demand_user_id, $amount, 1,$design_user_id, '【' . $item_info['name'] . '】' . '向设计公司支付部分项目款');
            //设计公司流水记录
            $fund_log->inFund($design_user_id, $amount, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '收到部分项目款');

            $tools = new Tools();
            //通知需求公司
            $tools->message($demand_user_id, '【' . $item_info['name'] . '】' . '向设计公司支付部分项目款');
            //通知设计公司
            $tools->message($demand_user_id, '【' . $item_info['name'] . '】' . '收到部分项目款');
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
        }
        DB::commit();
    }

}
