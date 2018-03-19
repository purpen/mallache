<?php
namespace App\Http\Transformer;

use App\Models\PanDirector;
use League\Fractal\TransformerAbstract;

class YunpanListTransformer extends TransformerAbstract
{
    public function transform(PanDirector $director){
        return $director->info();
    }
}