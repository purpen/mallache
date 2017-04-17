<?php

namespace App\Http\Transformer;

use App\Models\Item;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        $data = $this->info($item);
        return $data;
    }

    /**
     * 判断并添加对应的详细信息
     *
     * @param Item $item
     * @return mixed|string
     */
    public function info(Item $item)
    {
        $info = '';
        switch ((int)$item->design_type){
            case 1:
            case 2:
            case 3:
                $info = $item->productDesign;
                return [
                    'id' => $item->id,
                    'design_type' => $item->design_type,
                    'status' => $item->status,
                    'field' => $info->field,
                    'industry' => $info->industry,
                    'name' => $info->name,
                    'product_features' => $info->product_features,
                    'competing_product' => $info->competing_product,
                    'design_cost' => $info->design_cost,
                    'province' => $info->province,
                    'city' => $info->city,
                    'image' => $info->image,
                ];
                break;
            case 4:
            case 5:
                $info = $item->uDesign;
                return [
                    'id' => $item->id,
                    'design_type' => $item->design_type,
                    'status' => $item->status,
                    'system' => $info->system,
                    'design_content' => $info->design_content,
                    'name' => $info->name,
                    'stage' => $info->stage,
                    'complete_content' => $info->complete_content,
                    'other_content' => $info->other_content,
                    'design_cost' => $info->design_cost,
                    'province' => $info->province,
                    'city' => $info->city,
                    'image' => $info->image,
                ];
                break;
        }
        return [];
    }
}