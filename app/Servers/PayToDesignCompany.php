<?php

namespace App\Servers;

use App\Helper\ItemCommissionAction;
use App\Helper\Tools;
use App\Models\FundLog;
use App\Models\Invoice;
use App\Models\ItemCommission;
use App\Models\User;

class PayToDesignCompany
{

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    //减少需求公司账户金额（总金额、冻结金额）
    protected function subDemandPrice($user_id, $amount)
    {
        $user_model = new User();
        $user_model->totalAndFrozenDecrease($user_id, $amount);
    }

    //增加设计公司账户金额（总金额）
    protected function addDesignPrice($user_id, $amount)
    {
        $user_model = new User();
        //增加设计公司账户总金额
        $user_model->totalIncrease($user_id, $amount);
    }

    // 向设计公司支付项目款
    public function pay()
    {
        if ($this->invoice->type != 1 || $this->invoice->company_type != 2) {
            throw new \Exception("invoice error", 403);
        }

        switch ($this->invoice->pay_type) {
            case 1:
                $this->payFirst();
                break;
            case 2:
                $this->payStage();
                break;
        }
    }

    // 支付项目首付款
    protected function payFirst()
    {
        $item = $this->invoice->item;

        // 判断合同版本是否符合要求
        if ($item->contract->version != 1) {
            throw new \Exception("contract version error", 403);
        }

        $demand_user_id = $item->user_id;
        $item_info = $item->itemInfo();
        //需求公司支付金额
        $amount = $item->contract->first_payment;

        //修改项目剩余项目款
        $item->rest_fund = bcsub($item->rest_fund, $amount, 2);
        $item->save();

        //减少需求公司账户金额（总金额、冻结金额）
        $this->subDemandPrice($demand_user_id, $amount);

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

        // 增值税扣点金额
        $quotation = $item->quotation;
        $tax = $quotation->getTax();
        // 收取项目增值税扣点
        ItemCommission::createCommission(2, $item->id, $design_company_id, $item->price, $tax);


        // 设计公司收到金额（扣除平台佣金）
        $design_amount = bcsub($amount, $commission, 2);
        // 设计公司收到金额（扣除增值税扣点）
        $design_amount = bcsub($design_amount, $tax, 2);
        // 设计公司收到金额
        $this->addDesignPrice($design_user_id, $design_amount);


        $fund_log = new FundLog();
        //需求公司流水记录
        $fund_log->outFund($demand_user_id, $amount, 1, $design_user_id, '【' . $item_info['name'] . '】' . '向设计公司支付项目首付款');
        //设计公司流水记录（项目首付款全款）
        $fund_log->inFund($design_user_id, $amount, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '收到项目首付款');

        if ($commission > 0) {
            //扣除佣金 设计公司流水记录
            $fund_log->outFund($design_user_id, $commission, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '平台扣除佣金');
        }

        if ($tax > 0) {
            //扣除税点 设计公司流水记录
            $fund_log->outFund($design_user_id, $tax, 1, $demand_user_id, '【' . $item_info['name'] . '】' . '平台扣除税点');
        }


        $tools = new Tools();
        //通知需求公司
        $title = '支付首付款';
        $content = '【' . $item_info['name'] . '】项目已向设计公司支付项目首付款';
        $tools->message($demand_user_id, $title, $content, 3, null);
        Tools::sendSmsToPhone($item->phone, $content);

        //通知设计公司
        $title1 = '收到首付款';
        $content1 = '【' . $item_info['name'] . '】项目已收到项目首付款';
        $tools->message($design_user_id, $title1, $content1, 3, null);
        Tools::sendSmsToPhone($design_phone, $content1);

        if ($commission > 0) {
            //扣除佣金 设计公司
            $title2 = '平台扣除佣金';
            $content2 = '【' . $item_info['name'] . '】项目已平台扣除佣金';
            $tools->message($design_user_id, $title2, $content2, 3, null);
            Tools::sendSmsToPhone($design_phone, $content2);
        }
    }

    // 支付项目阶段款
    protected function payStage()
    {

        // 关联项目
        $item = $this->invoice->item;

        // 判断合同版本是否符合要求
        if ($item->contract->version != 1) {
            throw new \Exception("contract version error", 403);
        }

        // 关联阶段
        $item_stage = $this->invoice->itemStage;


        // 支付金额
        $amount = $item_stage->amount;
        // 需求用户ID
        $demand_user_id = $item->user_id;
        $demand_phone = $item->phone;

        //设计公司用户ID
        $design_user_id = $item->designCompany->user_id;
        $design_phone = $item->designCompany->phone;

        //修改项目剩余项目款
        $item->rest_fund = bcsub($item->rest_fund, $item_stage->amount, 2);
        $item->save();


        //减少需求公司账户金额（总金额、冻结金额）
        $this->subDemandPrice($demand_user_id, $amount);

        //增加设计公司账户总金额
        $this->addDesignPrice($design_user_id, $amount);

        // 更新阶段支付设计公司项目款状态
        $item_stage->pay_design_status = 1;
        $item_stage->save();

        $item_info = $item->itemInfo();
        $fund_log = new FundLog();
        //需求公司资金流水记录
        $fund_log->outFund($demand_user_id, $amount, 1, $design_user_id, '支付【' . $item_info['name'] . '】项目阶段项目款');
        //设计公司资金流水记录
        $fund_log->inFund($design_user_id, $amount, 1, $demand_user_id, '收到【' . $item_info['name'] . '】项目阶段项目款');

        $tools = new Tools();
        //通知需求公司
        $title1 = '支付阶段项目款';
        $content1 = '已支付【' . $item_info['name'] . '】项目阶段项目款';
        $tools->message($demand_user_id, $title1, $content1, 3, null);
        // 短信通知需求公司
        Tools::sendSmsToPhone($demand_phone, $content1);

        //通知设计公司
        $title2 = '收到阶段项目款';
        $content2 = '已收到【' . $item_info['name'] . '】项目阶段项目款';
        $tools->message($design_user_id, $title2, $content2, 3, null);
        // 短信通知设计公司
        Tools::sendSmsToPhone($design_phone, $content2);
    }

}