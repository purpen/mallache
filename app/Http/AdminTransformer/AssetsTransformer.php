<?php
namespace App\Http\AdminTransformer;

use App\Models\AssetModel;
use League\Fractal\TransformerAbstract;

class AssetsTransformer extends TransformerAbstract
{
    public function transform(AssetModel $assets)
    {

        return [
            'id' => $assets->id,
            'user_id' => $assets->user_id,
            'target_id' => $assets->target_id,
            'type' => $assets->type,
            'name' => $assets->name,
            'summary' => $assets->summary,
            'random' => $assets->random,
            'path' => $assets->path,
            'created_at' => $assets->created_at,
            'size' => $assets->size,
            'width' => $assets->width,
            'height' => $assets->height,
            'mime' => $assets->mime,
            'domain' => $assets->domain,
            'status' => $assets->status,

        ];
    }
}