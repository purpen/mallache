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
    public function createPullInvoice($pay_type, $target_id, $price, $item_id, $item_stage_id)
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
            'item_stage_id' => $this->item_stage_id,
            'user_id' => $this->user_id,
            'summary' => $this->summary,
            'status' => $this->status,
        ];
    }

    // 一对多相对关联项目表
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    // 一对多相对关联项目表
    public function itemStage()
    {
        return $this->belongsTo('App\Models\ItemStage', 'item_stage_id');
    }
}