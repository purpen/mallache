<?php

namespace App\Http\Transformer;

use App\Models\DesignNotice;
use League\Fractal\TransformerAbstract;

class DesignNoticeTransformer extends TransformerAbstract
{
    public function transform(DesignNotice $design_notice)
    {
        return $design_notice->info();
    }
}