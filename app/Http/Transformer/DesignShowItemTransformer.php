<?php
namespace App\Http\Transformer;

use App\Models\Item;
use App\Models\QuotationModel;
use League\Fractal\TransformerAbstract;

class DesignShowItemTransformer extends TransformerAbstract
{
    protected $design_company_id;

    public function __construct($design_company_id)
    {
        $this->design_company_id = $design_company_id;
    }

    public function transform(Item $item)
    {
        $data = $item->itemInfo();
        return [
            'item' => $data,
            'quotation' => $this->quotation($item->id),
            'contract' => $item->contract,
        ];
    }

    //设计公司报价单
    protected function quotation($item_id)
    {
        return QuotationModel::where(['item_demand_id' => $item_id, 'design_company_id' => $this->design_company_id])->first();
    }
}