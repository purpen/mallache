<?php

namespace App\Models;

use App\Helper\NumberToHanZi;
use Illuminate\Database\Eloquent\Model;

class Contract extends BaseModel
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'contract';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'item_demand_id',
        'design_company_id',
        'demand_company_name',
        'demand_company_address',
        'demand_company_phone',
        'demand_company_legal_person',
        'design_company_name',
        'design_company_address',
        'design_company_phone',
        'design_company_legal_person',
        'thn_company_name',
        'thn_company_address',
        'thn_company_phone',
        'thn_company_legal_person',
        'item_content',
        'total',
        'design_work_content',
        'status',
        'unique_id',
        'title',
        'warranty_money',
        'first_payment',
        'warranty_money_proportion',
        'first_payment_proportion',
        'commission_rate',
        'commission',
        'demand_pay_limit',
        'thn_pay_limit',
        'version',
    ];

    //相对关联 项目表
    public function itemDemand()
    {
        return $this->belongsTo('App\Models\Item', 'item_demand_id');
    }

    //相对关联 设计公司表
    public function designCompany()
    {
        return $this->belongsTo('App\Models\DesignCompanyModel', 'design_company_id');
    }

    public function info()
    {
        return [
            'id' => intval($this->id),
            'item_demand_id' => intval($this->item_demand_id),
            'design_company_id' => intval($this->design_company_id),
            'demand_company_name' => strval($this->demand_company_name),
            'demand_company_address' => strval($this->demand_company_address),
            'demand_company_phone' => strval($this->demand_company_phone),
            'demand_company_legal_person' => strval($this->demand_company_legal_person),
            'design_company_name' => strval($this->design_company_name),
            'design_company_address' => strval($this->design_company_address),
            'design_company_phone' => strval($this->design_company_phone),
            'design_company_legal_person' => strval($this->design_company_legal_person),
            'total' => strval($this->total),
            'total_han' => NumberToHanZi::numberToH($this->total),

            'status' => intval($this->status),
            'unique_id' => strval($this->unique_id),
            'item_name' => $this->item_name,
            'title' => strval($this->title),
            'warranty_money' => $this->warranty_money,
            'first_payment' => $this->first_payment,
            'warranty_money_proportion' => $this->warranty_money_proportion,
            'first_payment_proportion' => $this->first_payment_proportion,

            'thn_company_name' => strval($this->thn_company_name),
            'thn_company_address' => strval($this->thn_company_address),
            'thn_company_phone' => strval($this->thn_company_phone),
            'thn_company_legal_person' => strval($this->thn_company_legal_person),
            'commission' => $this->commission,
            'commission_han' => NumberToHanZi::numberToH($this->commission),
            'commission_rate' => $this->commission_rate,
            'demand_pay_limit' => $this->demand_pay_limit,
            'thn_pay_limit' => $this->thn_pay_limit,
            'version' => $this->version,
            'tax_price' => $this->tax_price,
        ];


    }
}
