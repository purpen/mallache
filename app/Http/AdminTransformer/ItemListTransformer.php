<?php

namespace App\Http\AdminTransformer;

use App\Models\DesignCompanyModel;
use App\Models\Item;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class ItemListTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        $type = $item->type;
        if ($type == 0) {
            $info = '';
        } else if ($type == 1) {
            $info = $item->productDesign ?? '';
        } else if ($type == 2) {
            $info = $item->uDesign ?? '';
        } else {
            $info = '';
        }

        $design_company_id = explode(',', $item->recommend);
        if (!empty($design_company_id)) {
            $designCompany = DesignCompanyModel::whereIn("id", $design_company_id)->get();
        } else {
            $designCompany = [];
        }

        $item->user;
        unset($item->productDesign, $item->uDesign);

        return [
            'item' => $item,
            'info' => $info,
            'designCompany' => $designCompany
        ];
    }
}