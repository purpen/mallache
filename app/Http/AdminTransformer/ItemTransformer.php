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
            'user' => [
                'id' => (int)$user->id,
                'type' => (int)$user->type,
                'account' => $user->account,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'item_sum' => $user->item_sum,
                'price_total' => floatValue($user->price_total),
                'price_frozen' => floatValue($user->price_frozen),
                'img' => $user->image,
                'design_company_id' =>$user->design_company_id,
            ],
            'item' => $item,
            'info' => $info,
        ];
    }
}