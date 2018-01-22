<?php
namespace App\Http\Transformer;

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
            'code' => $block->code,
            'content' => $block->content,
            'count' => $block->count,
        ];
    }
}
