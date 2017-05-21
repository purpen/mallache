<?php
namespace App\Http\AdminTransformer;

use App\Models\DesignCompanyModel;
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
        }
        else if($type == 2)
        {
            $info = $item->uDesign ?? '';
        }
        else
        {
            $info = '';
        }
        $design_company_id = explode(',' , $item->recommend);
        $designCompany = DesignCompanyModel::whereIn("id", $design_company_id)->get();
        $user = $item->user;
        unset($item->productDesign, $item->uDesign);
        return [
            'item' => $item,
            'info' => $info,
            'designCompany' => $designCompany
        ];
    }
}