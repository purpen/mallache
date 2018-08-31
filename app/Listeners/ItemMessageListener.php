<?php

namespace App\Listeners;

use App\Events\ItemStatusEvent;
use App\Helper\ItemCommissionAction;
use App\Helper\Tools;
use App\Jobs\SendOneSms;
use App\Models\DesignCompanyModel;
use App\Models\FundLog;
use App\Models\Item;
use App\Models\ItemCommission;
use App\Models\ItemRecommend;
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
        try {
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
                    $this->closeItem($item);
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
                //等待项目款托管,通知需求方打款
                case 8:
                    $this->demandPay($event);
                    break;
                //项目款已托管
                case 9:
                    //向设计公司通知
                    $this->demandTrustFunds($event);
                    break;
                //项目进行中
                case 11:
                    // 支付首付款（合同版本：0）
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
                    // 向设计公司支付项目剩余款项（合同版本：0）
                    $this->payRestFunds($event);
                    break;
                //通知设计公司项目已评价
                case 22:
                    $this->designEvaluate($event);
                    break;
            }
        } catch (\Exception $e) {
            Log::error($e);
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
        $title = '查看匹配结果';
        $content = '您好，平台为您的【' . $item_info['name'] . '】项目匹配了适合您的设计服务供应商';
        Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);

        $message_content = '已匹配设计服务商，请选择。感谢您的信任，如有疑问欢迎致电 ';
        //给项目联系人发送信息
        Tools::sendSmsToPhone($item->phone, $message_content, $item->source);
    }

    /**
     * 向设计公司推送项目
     */
    public function pushItemToDesign(ItemStatusEvent $event)
    {
        try {
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
                Tools::message($user_id_arr[$i], $title, $content, 2, $item->id, $item->status);
                $message_content = '收到新项⽬，请报价并等待客户确认。感谢您的信任，如有疑问欢迎致电 ';
                //短信通知设计公司有新项目推送
                Tools::sendSmsToPhone($phone_arr[$i], $message_content);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }

    }

    /**
     * 用户确认报价单选定设计公司、向选中设计公司和落选的设计公司发送系统消息
     */
    public function trueDesign(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //选定公司ID
        $design_company_id = $event->design_company_id['yes'];
        $design = DesignCompanyModel::find($design_company_id);
        $title = '确认报价';
        $content = '【' . $item_info['name'] . '】' . '项目报价已确认，请尽快编辑并向对方发送项目合同';
        Tools::message($design->user_id, $title, $content, 2, $item->id, $item->status);

        $design_m_content = '客户已确认报价，请在平台填报合同。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($design->phone, $design_m_content);

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
            Tools::message($user_id_arr[$i], $title, $content, 1, null);
            $no_content = '需求方已选择其他设计公司。感谢您的信任，如有疑问欢迎致电 ';
            Tools::sendSmsToPhone($phone_arr[$i], $no_content);
        }
    }

    //项目匹配失败-通知用户
    public function itemFail(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $title = '项目对接失败，请重新发布需求';
        $content = '【' . $item_info['name'] . '】' . '未达成合作，匹配失败';
        Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);

        Tools::sendSmsToPhone($item->phone, $content, $item->source);
    }

    //设计公司提交合同，通知需求公司
    public function designSubmitContract(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $title = '收到合同';
        $content = '收到设计公司发来【' . $item_info['name'] . '】项目合同书请查看并确认或与设计服务供应商沟通做进一步修改';
        Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);

        $message_content = '收到合同，请查阅。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($item->phone, $message_content, $item->source);
    }

    //需求公司确认合同，通知设计公司
    public function demandTrueContract(ItemStatusEvent $event)
    {
        try {
            $item = $event->item;
            $item_info = $item->itemInfo();

            //获取设计公司user_id
            $user_id = $item->designCompany->user_id;
            $phone = $item->designCompany->phone;

            $title = '合同确认';
            $content = '您与' . $item->company_name . '公司的【' . $item_info['name'] . '】合同已订立，请按合同规定在收到项目款后开始设计工作';
            Tools::message($user_id, $title, $content, 2, $item->id, $item->status);

            $message_content = '合同已订⽴，等待客户⽀付项⽬款。感谢您的信任，如有疑问欢迎致电 ';
            Tools::sendSmsToPhone($phone, $message_content);
        } catch (\Exception $e) {
            Log::error($e);
        }

    }

    //需求公司已托管项目首付款
    public function demandTrustFunds(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //获取设计公司user_id
        $user_id = $item->designCompany->user_id;
        $phone = $item->designCompany->phone;

        //通知设计公司
        $title = '项目已托管项目首付款';
        $content = '【' . $item_info['name'] . '】' . '项目首付款已托管';
        Tools::message($user_id, $title, $content, 2, $item->id, $item->status);
        $design_m_content = '客户已⽀付项⽬款，请启动项目。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($phone, $design_m_content);
        //需求公司
        $demand_m_content = '项⽬款已支付，等待设计服务商启动。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($item->phone, $demand_m_content, $item->source);

    }

    //项目进行中 通知需求公司
    public function itemOngoing(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //系统消息通知需求公司
        $title = '项目进行中';
        $content = '设计服务供应商已开始【' . $item_info['name'] . '】项目，项目进行中，请及时跟进进度，保持与设计服务供应商的沟通';
        Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);

        $message_content = '设计服务商已启动项⽬。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($item->phone, $message_content, $item->source);
    }

    //项目已完成
    public function itemDone(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //系统消息通知需求公司
        $title = '项目验收';
        $content = '【' . $item_info['name'] . '】项目全部阶段已完成，请尽快确认支付项目尾款，并对服务进行评价';
        Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);

        $message_content = '项⽬已全部完成，请验收。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($item->phone, $message_content, $item->source);
    }

    //确认项目完成
    public function trueItemDone(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $design_company = DesignCompanyModel::find($item->design_company_id);
        $user_id = $design_company->user_id;

        //系统消息通知设计公司
        $title = '项目确认验收';
        $content = '需求方已对【' . $item_info['name'] . '】' . '项目确认验收';
        Tools::message($user_id, $title, $content, 2, $item->id, $item->status);

        $message_content = '客户已验收项目。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($design_company->phone, $message_content);
    }

    //向设计公司支付项目剩余款项
    public function payRestFunds(ItemStatusEvent $event)
    {
        $item = $event->item;
        // 当版本不是0版本时，跳过
        if ($item->contract->version != 0) {
            return;
        }

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

            //通知需求公司
            $title = '支付尾款';
            $content = '【' . $item_info['name'] . '】项目已向设计公司支付项目尾款';
            Tools::message($demand_user_id, $title, $content, 3, null);
            Tools::sendSmsToPhone($item->phone, $content, $item->source);

            //通知设计公司
            $title1 = '收到尾款';
            $content1 = '【' . $item_info['name'] . '】项目已收到项目尾款';
            Tools::message($design_user_id, $title1, $content1, 3, null);
            Tools::sendSmsToPhone($design_phone, $content);

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
        // 当版本不是0版本时，跳过
        if ($item->contract->version != 0) {
            return;
        }

        DB::beginTransaction();
        $user_model = new User();

        try {
            $demand_user_id = $item->user_id;
            $item_info = $item->itemInfo();
            //支付金额
            $amount = $item->contract->first_payment;

            //修改项目剩余项目款
            $item->rest_fund = bcsub($item->rest_fund, $amount, 2);
            $item->save();

            //减少需求公司账户金额（总金额、冻结金额）
            $user_model->totalAndFrozenDecrease($demand_user_id, $amount);


            //设计公司用户ID
            $design_user_id = $item->designCompany->user_id;
            $design_phone = $item->designCompany->phone;
            $design_company_id = $item->designCompany->id;

            // 计算平台佣金
            $commission = ItemCommissionAction::getCommission($item);
            if ($amount < $commission) {
                throw new \Exception("首付款金额低于平台佣金", 500);
            }
            // 收取佣金记录
            ItemCommission::createCommission(1, $item->id, $design_company_id, $item->price, $commission);

            // 设计公司收到金额（扣除平台佣金）
            $design_amount = bcsub($amount, $commission, 2);
            //增加设计公司账户总金额
            $user_model->totalIncrease($design_user_id, $design_amount);


            $fund_log = new FundLog();
            //需求公司流水记录
            $fund_log->outFund($demand_user_id, $amount, 1, $design_user_id, '【' . $item_info['name'] . '】' . '向设计公司支付项目首付款');
            //设计公司流水记录
            $fund_log->inFund($design_user_id, $design_amount, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '收到项目首付款');

            if ($commission > 0) {
                //扣除佣金 设计公司流水记录
                $fund_log->outFund($design_user_id, $commission, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '平台扣除佣金');
            }

            //通知需求公司
            $title = '支付首付款';
            $content = '【' . $item_info['name'] . '】项目已向设计公司支付项目首付款';
            Tools::message($demand_user_id, $title, $content, 3, null);
            Tools::sendSmsToPhone($item->phone, $content, $item->source);

            //通知设计公司
            $title1 = '收到首付款';
            $content1 = '【' . $item_info['name'] . '】项目已收到项目首付款';
            Tools::message($design_user_id, $title1, $content1, 3, null);
            Tools::sendSmsToPhone($design_phone, $content1);

            if ($commission > 0) {
                //扣除佣金 设计公司
                $title2 = '平台扣除佣金';
                $content2 = '【' . $item_info['name'] . '】项目已平台扣除佣金';
                Tools::message($design_user_id, $title2, $content2, 3, null);
                Tools::sendSmsToPhone($design_phone, $content2);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }

    }

    // 有新项目发布通知运营管理人员
    protected function newItemNotice(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $phone_arr = config('constant.new_item_send_phone');

        $text = '【太火鸟铟果】您好，铟果平台有新项目需求发布:“' . $item_info['name'] . '”，请及时登陆查看，并进行相关操作。';
        foreach ($phone_arr as $phone) {
            dispatch(new SendOneSms($phone, $text));
        }
    }

    //用户关闭项目通知设计公司
    protected function closeItem(Item $item)
    {
        $item_id = $item->id;
        $item_info = $item->itemInfo();

        $item_recommend_qt = ItemRecommend::where(['item_id' => $item_id, 'item_status' => 0])
            ->where('design_company_status', '!=', -1)
            ->get();
        if (!$item_recommend_qt->isEmpty()) {
            foreach ($item_recommend_qt as $qt) {
                $qt->item_status = -1;
                $qt->save();

            }

            $design_company_id_arr = $item_recommend_qt->pluck('design_company_id')->all();
            $designCompanies = DesignCompanyModel::whereIn('id', $design_company_id_arr)->get();
            // 需要通知的设计用户ID
            $user_id_arr = $designCompanies->pluck('user_id')->all();
            // 需要通知的设计公司手机
            $phone_arr = $designCompanies->pluck('phone')->all();

            //添加系统通知
            $title = '需求方已关闭项目';
            $content = '【' . $item_info['name'] . '】' . '需求方已关闭项目';

            $n = count($user_id_arr);
            for ($i = 0; $i < $n; ++$i) {
                Tools::message($user_id_arr[$i], $title, $content, 1, null);

                Tools::sendSmsToPhone($phone_arr[$i], $content);
            }
        }

    }

    /**
     * 通知需求公司托管项目资金
     */
    protected function demandPay(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        //通知需求公司
        $title = '请⽀付项⽬款';
        $content = '【' . $item_info['name'] . '】请⽀付项⽬款';
        Tools::message($item->user_id, $title, $content, 2, $item->id, $item->status);
        //通知需求公司支付项目款
        $message_content = '请⽀付项⽬款。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($item->phone, $message_content, $item->source);
    }

    /**
     *通知设计方已经评价
     */
    protected function designEvaluate(ItemStatusEvent $event)
    {
        $item = $event->item;
        $item_info = $item->itemInfo();

        $design_company = DesignCompanyModel::find($item->design_company_id);

        $user_id = $design_company->user_id;
        //系统消息通知设计公司
        $title = '项目已评价';
        $content = '需求方已对【' . $item_info['name'] . '】' . '项目已评价';
        Tools::message($user_id, $title, $content, 2, $item->id, $item->status);

        $message_content = '项目已评价。感谢您的信任，如有疑问欢迎致电 ';
        Tools::sendSmsToPhone($design_company->phone, $message_content);
    }
}
