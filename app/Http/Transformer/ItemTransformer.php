<?php

namespace App\Http\Transformer;

use App\Models\Item;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        return [
            'id' => $item->id,
            'design_type' => $item->design_type,
            'field' => $item->field,
            'info' => $this->info($item)
        ];
    }

    public function info(Item $item)
    {
        $info = '';
        switch ((int)$item->design_type){
            case 1:
            case 2:
            case 3:
                $info = $item->productDesign?? 'pro';
                break;
            case 4:
            case 5:
                $info = $item->uDesign?? 'ux';
                break;
        }
        return $info;
    }
}