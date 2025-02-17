<?php
namespace App\Http\AdminTransformer;

use App\Models\Classification;
use League\Fractal\TransformerAbstract;

class ClassificationTransformer extends TransformerAbstract
{
    public function transform(Classification $classification)
    {
        return [
            'id' => $classification->id,
            'name' => $classification->name,
            'type' => $classification->type,
            'type_value' => $classification->type_value,
            'status' => $classification->status,
        ];
    }
}