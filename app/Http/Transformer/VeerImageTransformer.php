<?php
namespace App\Http\Transformer;

use League\Fractal\TransformerAbstract;

class VeerImageTransformer extends TransformerAbstract
{
    public function transform($veerImages)
    {

        return [
            'id' => $veerImages->id,
            'title' => $veerImages->title,
            'content' => $veerImages->small_url,
            'classification_id' => $veerImages->preview_url,
            'classification_value' => $veerImages->veer_url,
            'status' => $veerImages->title,
        ];
    }
}