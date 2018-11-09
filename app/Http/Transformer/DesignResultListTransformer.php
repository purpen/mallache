<?php
namespace App\Http\Transformer;

use App\Models\DesignResult;
use League\Fractal\TransformerAbstract;

class DesignResultListTransformer extends TransformerAbstract
{
    public function transform(DesignResult $designResult)
    {
        return [
            'id' => $designResult->id,
            'title' => $designResult->title,
            'content' => $designResult->content,
            'cover_id' => $designResult->cover_id,
            'sell_type' => $designResult->sell_type,
            'price' => $designResult->price,
            'share_ratio' => $designResult->share_ratio,
            'design_company_id' => $designResult->design_company_id,
            'user_id' => $designResult->user_id,
            'cover' => $designResult->cover,
            'status' => $designResult->status,
            'thn_cost' => $designResult->thn_cost,
            'follow_count' => $designResult->follow_count,
            'demand_company_id' => $designResult->demand_company_id,
            'purchase_user_id' => $designResult->purchase_user_id,
            'created_at' => $designResult->created_at,
            'updated_at' => $designResult->updated_at,
            'sell' => $designResult->sell,
            'contacts' => $designResult->contacts,
            'contact_number' => $designResult->contact_number,
            'is_follow' => $designResult->is_follow,
        ];
    }
}