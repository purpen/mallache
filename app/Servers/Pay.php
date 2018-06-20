<?php

namespace App\Servers;

use App\Events\ItemStatusEvent;
use App\Helper\ItemCommissionAction;
use App\Models\DemandCompany;
use App\Models\FundLog;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemStage;
use App\Models\PayOrder;
use App\Models\User;

// 需求公司支付服务模块
class Pay
{
    /**
     * 支付单实例
     * @var PayOrder
     */
    public $pay_order;

    public function __construct(PayOrder $pay_order)
    {
        $this->pay_order = $pay_order;
    }

    /**
     * 需求用户付款后增加钱包账户总金额和冻结金额
     */
    protected function addPrice()
    {
        $user = new User();
        $user->totalAndFrozenIncrease($this->pay_order->user_id, $this->pay_order->amount);
    }

    // 支付单成功支付
    public function paySuccess()
    {
        /**
         * 判断付款类型,修改对应项目付款状态
         */
        switch ($this->pay_order->type) {
            // 项目首付款
            case 3:
                $this->itemFirstPay();
                break;

            // 项目阶段付款
            case 4:
                $this->itemStagePay();
                break;
        }

    }

    // 收到项目首付款
    protected function itemFirstPay()
    {
        $this->addPrice();

        $item = Item::query()->find($this->pay_order->item_id);

        //修改项目状态为项目首付款已托管
        $item->status = 9;
        $item->rest_fund = bcadd($item->rest_fund, $this->pay_order->amount, 2); // 项目当前金额
        $item->save();
        event(new ItemStatusEvent($item));

        $item_info = $item->itemInfo();
        //资金流水记录
        $fund_log = new FundLog();
        $fund_log->inFund($this->pay_order->user_id, $this->pay_order->amount, $this->pay_order->pay_type, $this->pay_order->uid, '【' . $item_info['name'] . '】项目首付款托管');

        // 生成需要给需求公司开发票信息
        $demand_company = DemandCompany::query()->where('user_id', $this->pay_order->user_id)->first();
        $demand_invoice = new Invoice();
        $demand_invoice->createPushInvoice(1, $demand_company->id, $this->pay_order->amount, $this->pay_order->item_id, null);


        // 计算平台佣金
        $commission = ItemCommissionAction::getCommission($item);
        // 设计公司收到金额（扣除平台佣金）
        $design_amount = bcsub($this->pay_order->amount, $commission, 2);

        // 生成需要收取设计公司发票的信息
        $design_invoice = new Invoice();
        $design_invoice->createPullInvoice(1, $item->design_company_id, $design_amount, $this->pay_order->item_id, null);
    }

    // 收到项目阶段付款
    protected function itemStagePay()
    {
        $this->addPrice();

        $item = Item::query()->find($this->pay_order->item_id);
        $item->rest_fund = bcadd($item->rest_fund, $this->pay_order->amount, 2); // 增加项目当前余额
        $item->save();

        // 阶段金额已支付
        $item_stage = ItemStage::find($this->pay_order->item_stage_id);
        $item_stage->pay_status = 1;
        $item_stage->save();

        $item_info = $item->itemInfo();
        //资金流水记录
        $fund_log = new FundLog();
        $fund_log->inFund($this->pay_order->user_id, $this->pay_order->amount, $this->pay_order->pay_type, $this->pay_order->uid, '【' . $item_info['name'] . '】项目阶段款托管');


        // 生成需要给需求公司开发票信息
        $demand_company = DemandCompany::query()->where('user_id', $this->pay_order->user_id)->first();
        $demand_invoice = new Invoice();
        $demand_invoice->createPushInvoice(2, $demand_company->id, $this->pay_order->amount, $this->pay_order->item_id, null);


        // 生成需要收取设计公司发票的信息
        $design_invoice = new Invoice();
        $design_invoice->createPullInvoice(2, $item->design_company_id, $this->pay_order->amount, $this->pay_order->item_id, $this->pay_order->item_id);
    }

}