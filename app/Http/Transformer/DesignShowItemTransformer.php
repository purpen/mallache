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

        // 添加 项目完成后支付金额和其他金额信息
        $data['warranty_money'] = sprintf("%0.2f", $item->price * config("constant.warranty_money"));
        $data['other_money'] = sprintf("%0.2f", $item->price - $item->warranty_money);

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