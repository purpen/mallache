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
        $data = $contract->info();
        $data['item_stage'] = $this->itemStage($contract->item_demand_id);
        return $data;
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
                    'amount' => number_format($v->amount, 2, '.', ''),
                    'time' => $v->time,
                    'sort' => $v->sort,
                ];
            }
            return $data;
        }
    }
}