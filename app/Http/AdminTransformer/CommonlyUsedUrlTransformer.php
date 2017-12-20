<?php

namespace App\Http\AdminTransformer;

use App\Models\CommonlyUsedUrl;
use League\Fractal\TransformerAbstract;

class CommonlyUsedUrlTransformer extends TransformerAbstract
{
    public function transform(CommonlyUsedUrl $commonlyUsedUrl)
    {
        return [
            'id' => $commonlyUsedUrl->id,
            'type' => $commonlyUsedUrl->type,
            'type_value' => $commonlyUsedUrl->type_value,
            'url' => $commonlyUsedUrl->url,
            'title' => $commonlyUsedUrl->title,
            'user_id' => $commonlyUsedUrl->user_id,
            'cover_id' => $commonlyUsedUrl->cover_id,
            'cover' => $commonlyUsedUrl->cover,
            'summary' => $commonlyUsedUrl->summary,
            'status' => $commonlyUsedUrl->status,
            'created_at' => $commonlyUsedUrl->created_at,
        ];
    }
}
