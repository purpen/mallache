<?php

namespace App\Http\JdTransformer;

use App\Models\DesignCompanyModel;
use App\Models\Item;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
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

        $item_recommends = $item->itemRecommend;

        $recommend = [];
        foreach ($item_recommends as $item_recommend) {
            $recommend[] = [
                'status' => ($item_recommend->itemStatus())['status'],
                'status_value' => ($item_recommend->itemStatus())['status_value'],
                'item_status' => $item_recommend->item_status,
                'item_status_value' => $item_recommend->item_status_value,
                'design_company_status' => $item_recommend->design_company_status,
                'design_company_status_value' => $item_recommend->design_company_status_value,
                'design_company' => $item_recommend->designCompany,
                'quotation' => $item_recommend->quotation ? $item_recommend->quotation->info() : null,
            ];
        }

        $item_stages = $item->itemStage;
        $item_stage = [];
        foreach ($item_stages as $v) {
            $item_stage[] = $v->info();
        }


        $item->user;
        unset($item->productDesign, $item->uDesign);

        return [
            'item' => $item,
            'info' => $info,
            'designCompany' => $designCompany,
            'recommend' => $recommend,
            'item_stage' => $item_stage,
            'quotation' => $item->quotation ? $item->quotation->info() : null,
            'contract' => $item->contract ? $item->contract->info() : null,
        ];
    }
}