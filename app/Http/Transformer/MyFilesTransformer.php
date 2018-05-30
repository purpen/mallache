<?php

namespace App\Http\Transformer;

use App\Models\PanDirector;
use League\Fractal\TransformerAbstract;

class MyFilesTransformer extends TransformerAbstract
{
    public function transform(PanDirector $pan_director)
    {
        $arr = $pan_director->info();
        $arr['item_name'] = $pan_director->item ? $pan_director->item->itemInfo()['name'] : null;

        return $arr;
    }
}