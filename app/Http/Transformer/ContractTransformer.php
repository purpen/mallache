<?php

namespace App\Http\Transformer;

use App\Helper\NumberToHanZi;
use App\Models\Contract;
use App\Models\ItemStage;
use League\Fractal\TransformerAbstract;

class ContractTransformer extends TransformerAbstract
{
    public function transform(Contract $contract)
    {
        return [
            'id' => intval($contract->id),
            'item_demand_id' => intval($contract->item_demand_id),
            'design_company_id' => intval($contract->design_company_id),
            'demand_company_name' => strval($contract->demand_company_name),
            'demand_company_address' => strval($contract->demand_company_address),
            'demand_company_phone' => strval($contract->demand_company_phone),
            'demand_company_legal_person' => strval($contract->demand_company_legal_person),
            'design_company_name' => strval($contract->design_company_name),
            'design_company_address' => strval($contract->design_company_address),
            'design_company_phone' => strval($contract->design_company_phone),
            'design_company_legal_person' => strval($contract->design_company_legal_person),
            'total' => strval($contract->total),
            'total_han' => NumberToHanZi::numberToH($this->total),
//            'item_content' => $contract->item_content,
//            'design_work_content' => strval($contract->design_work_content),
            'status' => intval($contract->status),
            'unique_id' => strval($contract->unique_id),
            'item_name' => $contract->item_name,
            'title' => strval($contract->title),
            'warranty_money' => $contract->warranty_money,
            'first_payment' => $contract->first_payment,
            'warranty_money_proportion' => $contract->warranty_money_proportion,
            'first_payment_proportion' => $contract->first_payment_proportion,
            'item_stage' => $this->itemStage($contract->item_demand_id),

            'thn_company_name' => strval($contract->thn_company_name),
            'thn_company_address' => strval($contract->thn_company_address),
            'thn_company_phone' => strval($contract->thn_company_phone),
            'thn_company_legal_person' => strval($contract->thn_company_legal_person),
            'commission' => $contract->commission,
            'commission_han' => NumberToHanZi::numberToH($this->commission),
            'commission_rate' => $contract->commission_rate,
        ];
    }

    protected function itemStage($item_id)
    {
        $item_stage = ItemStage::where('item_id', $item_id)->orderBy('sort', 'asc')->get();
        if (!$item_stage) {
            return null;
        } else {
            /*id	int(10)	否
            item_id	int(10)	否		项目ID
            design_company_id	int(10)	否		设计公司ID
            title	varchar(50)	否		阶段名称
            content	varchar(500)	否		内容描述
            summary	varcha(100)	是	''	备注
            status	tinyint(4)	否	0	项目阶段状态：0.关闭; 1.发布；
            percentage	decimal(10,2)	否	0	项目金额百分比
            amount	decimal(10,2)	否	0	金额
            time	varchar(20)	否	''	工作日
            confirm	tinyint(4)	是	0	项目发布方是否确认。 0.未确认；1.已确认；*/
            $data = [];
            foreach ($item_stage as $v) {
                $data[] = [
                    'item_id' => $v->item_id,
                    'design_company_id' => $v->design_company_id,
                    'title' => $v->title,
                    'content' => $v->array_content,
                    'summary' => $v->summary,
                    'percentage' => $v->percentage,
                    'amount' => $v->amount,
                    'time' => $v->time,
                    'sort' => $v->sort,
                ];
            }
            return $data;
        }
    }
}