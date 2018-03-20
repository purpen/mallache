<?php

namespace App\Http\Transformer;

use App\Models\RecycleBin;
use League\Fractal\TransformerAbstract;

class RecycleBinTransformer extends TransformerAbstract
{

    public function transform(RecycleBin $recycle_bin)
    {
        return $recycle_bin->info();
    }

}
