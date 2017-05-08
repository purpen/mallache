<?php
namespace App\Http\AdminTransformer;

use App\Models\Item;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        $type = $item->type;
        if($type == 0)
        {
            $info = '';
        }
        else if($type == 1)
        {
            $info = $item->productDesign ?? '';
            unset($item->productDesign);
        }
        else if($type == 2)
        {
            $info = $item->uDesign ?? '';
            unset($item->uDesign);
        }
        else
        {
            $info = '';
        }

        $user = $item->user;

        return [
            'item' => $item,
            'info' => $info,
        ];
    }
}