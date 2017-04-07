<?php

namespace App\Http\Transformer;

use App\Models\QuotationModel;
use League\Fractal\TransformerAbstract;

class QuotationTransformer extends TransformerAbstract
{
    /*
        id	                int(10)	        否
        item_demand_id	    int(10)	        否		项目需求ID
        design_company_id	int(10)	        否		设计公司id
        price	            decimal(10,2)	否		报价
        summary	            varchar(500)	否		报价说明
        status	            tinyint(10)	    否	0	状态：1.已提交
    */

    public function transform(QuotationModel $quotation)
    {
        return [
            'id' => intval($quotation->id),
            'item_demand_id' => intval($quotation->item_demand_id),
            'design_company_id' => intval($quotation->design_company_id),
            'price' => strval($quotation->price),
            'summary' => strval($quotation->summary),
            'status' => intval($quotation->status),
        ];
    }
}