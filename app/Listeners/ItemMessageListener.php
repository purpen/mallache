<?php

namespace App\Listeners;

use App\Events\ItemStatusEvent;
use App\Helper\Tools;
use App\Jobs\SendOneSms;
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
     * @param  ItemStatusEvent $event
     * @return void
     */
    public function handle(ItemStatusEvent $event)
    {
        $item = $event->item;

        //记录项目状态变化时间
        $item->statusTime($item->status);

        switch ($item->status) {
            //项目匹配失败
            case -2:
                $this->itemFail($event);
                break;
            //用户关闭项目
            case -1:
                break;
            //创建项目
            case 1:
                break;
            //发布项目
            case 2:
                // 通知平台运营人员
                $this->newItemNotice($event);
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
                break;
            //项目进行中
            case 11:
                // 支付首付款
                $this->payFirstPayment($event);
                //通知需求公司
                $this->itemOngoing($event);
                break;
            // 项目已完成
            case 15:
                //通知需求公司
                $this->itemDone($event);
                break;
            //需求方 验收完成
            case 18:
                // 确认项目完成,通知设计公司
                $this->trueItemDone($event);
                // 向设计公司支付项目剩余款项
                $this->payRestFunds($event);
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
        $title = '查看匹配结果';
        $content = '您好，铟果平台为您的【' . $item_info['name'] . '】项目匹配了适合您的设计服务供应商';
        $tools->message($item->user_id, $title, $content, 2, $item->id);

        //给项目联系人发送信息
        $this->sendSmsToPhone($item->phone, $content);
    }

    /**
     * 向设计公司推送项目
     */
    public function pushItemToDesign(ItemStatusEvent $event)
    {
        $item = $event->item;
        $design_company_id = $event->design_company_id;

        $design_company_arr = DesignCompanyModel::select(['user_id', 'phone'])
            ->whereIn('id', $design_company_id)
            ->get();
        //设计公司ID 数组
        $user_id_arr = $design_company_arr->pluck('user_id')->all();

        //设计公司联系人手机
        $phone_arr = $design_company_arr->pluck('phone')->all();

        //添加系统通知
        $tools = new Tools();
        $n = count($user_id_arr);

        $title = '收到项目邀约';
        $content = '新收到【' . $item->itemInfo()['name'] . '】项目邀约';
        for ($i = 0; $i < $n; ++$i) {
            $tools->message($user_id_arr[$i], $title, $content, 2, $item->id);

            //短信通知设计公司有新项目推送
            $this->sendSmsToPhone($phone_arr[$i], $content);
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
        $title = '确认报价';
        $content = '【' . $item_info['name'] . '】' . '项目报价已确认，请尽快编辑并向对方发送项目合同';
        $tools->message($design->user_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($design->phone, $content);

        //拒绝公司ID
        $design_company_id_arr = $event->design_company_id['no'];
        //添加系统通知
        $designCompanies = DesignCompanyModel::whereIn('id', $design_company_id_arr)->get();

        $user_id_arr = $designCompanies->pluck('user_id')->all();
        $phone_arr = $designCompanies->pluck('phone')->all();

        //添加系统通知
        $title = '需求方拒绝报价';
        $content = '【' . $item_info['name'] . '】' . '需求方已选择其他设计公司';

        $n = count($user_id_arr);
        for ($i = 0; $i < $n; ++$i) {
            $tools->message($user_id_arr[$i], $title, $content, 1, null);

            $this->sendSmsToPhone($phone_arr[$i], $content);
        }
    }

    //项目匹配失败-通知用户
    public function itemFail(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $tools = new Tools();

        $title = '匹配失败';
        $content = '【' . $item_info['name'] . '】' . '未达成合作，匹配失败';
        $tools->message($item->user_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($item->phone, $content);
    }

    //设计公司提交合同，通知需求公司
    public function designSubmitContract(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $tools = new Tools();
        $title = '收到合同';
        $content = '收到设计公司发来【' . $item_info['name'] . '】项目合同书请查看并确认或与设计服务供应商沟通做进一步修改';
        $tools->message($item->user_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($item->phone, $content);
    }

    //需求公司确认合同，通知设计公司
    public function demandTrueContract(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //获取设计公司user_id
        $user_id = $item->designCompany->user_id;
        $phone = $item->designCompany->phone;

        $tools = new Tools();
        $title = '合同确认';
        $content = '您与' . $item->company_name . '公司的【' . $item_info['name'] . '】合同已订立，请按合同规定在收到项目款后开始设计工作';
        $tools->message($user_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($phone, $content);
    }

    //需求公司已托管项目资金
    public function demandTrustFunds(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //获取设计公司user_id
        $user_id = $item->designCompany->user_id;
        $phone = $item->designCompany->phone;

        //通知设计公司
        $tools = new Tools();
        $title = '项目已托管项目资金';
        $content = '【' . $item_info['name'] . '】' . '项目需求公司已托管项目资金，请开始设计工作';
        $tools->message($user_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($phone, $content);
    }

    //项目进行中 通知需求公司
    public function itemOngoing(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //系统消息通知需求公司
        $tools = new Tools();

        $title = '项目进行中';
        $content = '设计服务供应商已开始【' . $item_info['name'] . '】项目，项目进行中，请及时跟进进度，保持与设计服务供应商的沟通';
        $tools->message($item->user_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($item->phone, $content);
    }

    //项目已完成
    public function itemDone(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //系统消息通知需求公司
        $tools = new Tools();

        $title = '项目验收';
        $content = '【' . $item_info['name'] . '】项目全部阶段已完成，请尽快确认支付项目尾款，并对服务进行评价';
        $tools->message($item->user_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($item->phone, $content);
    }

    //确认项目完成
    public function trueItemDone(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $design_company = User::where('design_company_id', $item->design_company_id)->first();
        $design_company_id = $design_company->id;
        //系统消息通知设计公司
        $tools = new Tools();

        $title = '项目确认验收';
        $content = '需求方已对【' . $item_info['name'] . '】' . '项目确认验收';
        $tools->message($design_company_id, $title, $content, 2, $item->id);

        $this->sendSmsToPhone($design_company->phone, $content);
    }

    //向设计公司支付项目剩余款项
    public function payRestFunds(ItemStatusEvent $event)
    {
        $item = $event->item;

        DB::beginTransaction();
        $user_model = new User();

        try {
            $demand_user_id = $item->user_id;
            $item_info = $item->itemInfo();
            //支付金额
            $amount = $item->rest_fund;

            //修改项目剩余项目款为0
            $item->rest_fund = 0;
            $item->save();

            //减少需求公司账户金额（总金额、冻结金额）
            $user_model->totalAndFrozenDecrease($demand_user_id, $amount);

            //设计公司用户ID
            $design_user_id = $item->designCompany->user_id;
            $design_phone = $item->designCompany->phone;
            //增加设计公司账户总金额
            $user_model->totalIncrease($design_user_id, $amount);

            $fund_log = new FundLog();
            //需求公司流水记录
            $fund_log->outFund($demand_user_id, $amount, 1, $design_user_id, '【' . $item_info['name'] . '】' . '向设计公司支付项目尾款');
            //设计公司流水记录
            $fund_log->inFund($design_user_id, $amount, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '收到项目尾款');

            $tools = new Tools();
            //通知需求公司
            $title = '支付尾款';
            $content = '【' . $item_info['name'] . '】项目已向设计公司支付项目尾款';
            $tools->message($demand_user_id, $title, $content, 3, null);
            $this->sendSmsToPhone($item->phone, $content);

            //通知设计公司
            $title1 = '收到尾款';
            $content1 = '【' . $item_info['name'] . '】项目已收到项目尾款';
            $tools->message($design_user_id, $title1, $content1, 3, null);
            $this->sendSmsToPhone($design_phone, $content);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
        DB::commit();
    }

    /**
     * 向设计公司支付项目首付款
     *
     * @param ItemStatusEvent $event
     */
    public function payFirstPayment(ItemStatusEvent $event)
    {
        $item = $event->item;

        DB::beginTransaction();
        $user_model = new User();

        try {
            $demand_user_id = $item->user_id;
            $item_info = $item->itemInfo();
            //支付金额
            $amount = $item->contract->first_payment;

            //修改项目剩余项目款为0
            $item->rest_fund = $item->rest_fund - $amount;
            $item->save();

            //减少需求公司账户金额（总金额、冻结金额）
            $user_model->totalAndFrozenDecrease($demand_user_id, $amount);

            //设计公司用户ID
            $design_user_id = $item->designCompany->user_id;
            $design_phone = $item->designCompany->phone;
            //增加设计公司账户总金额
            $user_model->totalIncrease($design_user_id, $amount);

            $fund_log = new FundLog();
            //需求公司流水记录
            $fund_log->outFund($demand_user_id, $amount, 1, $design_user_id, '【' . $item_info['name'] . '】' . '向设计公司支付项目首付款');
            //设计公司流水记录
            $fund_log->inFund($design_user_id, $amount, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '收到项目首付款');

            $tools = new Tools();
            //通知需求公司
            $title = '支付首付款';
            $content = '【' . $item_info['name'] . '】项目已向设计公司支付项目首付款';
            $tools->message($demand_user_id, $title, $content, 3, null);
            $this->sendSmsToPhone($item->phone, $content);

            //通知设计公司
            $title1 = '收到首付款';
            $content1 = '【' . $item_info['name'] . '】项目已收到项目首付款';
            $tools->message($design_user_id, $title1, $content1, 3, null);
            $this->sendSmsToPhone($design_phone, $content);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
        DB::commit();
    }

    // 有新项目发布通知运营管理人员
    protected function newItemNotice(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $phone_arr = config('constant.new_item_send_phone');
        $text = '【太火鸟铟果】平台有新项目发布:【' . $item_info['name'] . '】';
        foreach ($phone_arr as $phone) {
            dispatch(new SendOneSms($phone, $text));
        }
    }

    // 短信发送内容拼接
    protected function phoneMessage($content)
    {
        return config('constant.sms_fix') . '您好，您在铟果平台的项目最新状态已更新，请您及时登录查看，并进行相应操作。感谢您的信任，如有疑问欢迎致电 '. config('constant.notice_phone') .'。';

//        return '您好，您在铟果平台的项目最新状态已更新为“' . $content . '”，请您及时登录铟果官网查看，并进行相应操作。感谢您的信任，如有疑问欢迎致电'. config('constant.notice_phone') .'。';
    }

    // 向用户发送系统信息
    protected function sendSmsToPhone($phone, $content)
    {
        $text = $this->phoneMessage($content);

        // 判断短信通知是否开启
        if(config('constant.sms_send')){
            dispatch(new SendOneSms($phone, $text));
        }
    }


}
