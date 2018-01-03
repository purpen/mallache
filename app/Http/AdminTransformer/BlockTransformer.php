<?php
namespace App\Http\AdminTransformer;

use App\Models\Block;
use League\Fractal\TransformerAbstract;

class BlockTransformer extends TransformerAbstract
{
    public function transform(Block $block)
    {
        return [
            'id' => $block->id,
            'name' => $block->name,
            'mark' => $block->mark,
            'type' => $block->type,
            'status' => $block->status,
            'user_id' => $block->user_id,
            'code' => $block->code,
            'content' => $block->content,
            'summary' => $block->summary,
            'count' => $block->count,
        ];
    }
}