<?php

namespace App\Models;

class Invoice extends BaseModel
{
    protected $table = 'invoice';

    /**
     * 创建待开发票记录
     *
     * @param $pay_type int 支付类型： 1. 首付款 2. 阶段款
     * @param $target_id int 公司ID
     * @param $price float 金额
     * @param $item_id int 项目ID
     * @param $item_stage_id int 项目阶段ID
     * @return Invoice
     */
    public function createPushInvoice($pay_type, $target_id, $price, $item_id, $item_stage_id)
    {
        $invoice = new self();
        $invoice->type = 2; // 2. 开
        $invoice->company_type = 1; // 需求公司
        $invoice->pay_type = $pay_type;
        $invoice->target_id = $target_id; // 公司ID
        $invoice->price = $price; // 金额
        $invoice->item_id = $item_id; //
        $invoice->item_stage_id = $item_stage_id; //
        $invoice->status = 1; //
        $invoice->save();

        return $invoice;
    }

    /**
     * 创建待收发票记录
     *
     * @param $pay_type int 支付类型： 1. 首付款 2. 阶段款
     * @param $target_id int 公司ID
     * @param $price float 金额
     * @param $item_id int 项目ID
     * @param $item_stage_id int 项目阶段ID
     * @return Invoice
     */
    public function createPullInvoice($pay_type, $target_id, $price, $item_id, $item_stage_id, $taxable_type, $invoice_type)
    {
        $invoice = new self();
        $invoice->type = 1; // 2. 开
        $invoice->company_type = 2; // 设计公司
        $invoice->pay_type = $pay_type;
        $invoice->target_id = $target_id; // 公司ID
        $invoice->price = $price; // 金额
        $invoice->item_id = $item_id; //
        $invoice->item_stage_id = $item_stage_id; //
        $invoice->status = 1;
        $invoice->taxable_type = $taxable_type;
        $invoice->invoice_type = $invoice_type;

        $invoice->save();

        return $invoice;
    }

    // 信息详情
    public function info()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'pay_type' => $this->pay_type,
            'company_type' => $this->company_type,
            'target_id' => $this->target_id,
            'company_name' => $this->company_name,
            'duty_number' => $this->duty_number,
            'price' => $this->price,
            'item_id' => $this->item_id,
            'item_name' => $this->item->name,
            'item_stage_id' => $this->item_stage_id,
            'item_stage_name' => $this->itemStage->title,
            'user_id' => $this->user_id,
            'summary' => $this->summary,
            'status' => $this->status,
            'taxable_type' => $this->taxable_type,
            'invoice_type' => $this->invoice_type,
            'logistics_name' => $this->logistics_name,
            'logistics_id' => $this->logistics_id,
            'logistics_number' => $this->logistics_number,
        ];
    }

    // 获取设计项目对应设计公司的发票信息列表
    public static function designInvoiceLists($item_id)
    {
        $lists = Invoice::query()
            ->where('item_id', $item_id)
            ->where('type', 1)
            ->where('company_type', 2)
            ->get();
        $data = [];
        foreach ($lists as $v) {
            $data[] = $v->info();
        }

        return $data;
    }

    // 获取设计项目对应需求公司的发票信息列表
    public static function demandInvoiceLists($item_id)
    {
        $lists = Invoice::query()
            ->where('item_id', $item_id)
            ->where('type', 2)
            ->where('company_type', 1)
            ->get();
        $data = [];
        foreach ($lists as $v) {
            $data[] = $v->info();
        }

        return $data;
    }

    // 一对多相对关联项目表
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    // 一对多相对关联项目阶段表
    public function itemStage()
    {
        return $this->belongsTo('App\Models\ItemStage', 'item_stage_id');
    }

    // 物流名称
    public function getLogisticsNameAttribute()
    {
        $logistics = config('constant.logistics');
        if (array_key_exists($this->logistics_id, $logistics)) {
            return $logistics[$this->logistics_id];
        }

        return '';
    }
}
