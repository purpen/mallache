<?php
namespace App\Http\Transformer;

use App\Models\Contract;
use App\Models\Item;
use League\Fractal\TransformerAbstract;

/**
 * 设计公司已合作项目转化器
 * Class DesignItemListTransformer
 * @package App\Http\Transformer
 */
class DesignItemListTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {

        return [
            'item' => $item->itemInfo(),
            'is_contract' => $this->searchContract($item),
        ];
    }

    protected function searchContract(Item $item)
    {
        if($item->status == 5){
            $count = Contract::where('item_demand_id', $item->id)->count();
            if($count){
                return 1;
            }else{
                return 0;
            }
        }else{
            return null;
        }
    }

}