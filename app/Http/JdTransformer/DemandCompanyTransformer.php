<?php
namespace App\Http\JdTransformer;

use App\Models\DemandCompany;
use League\Fractal\TransformerAbstract;

class DemandCompanyTransformer extends TransformerAbstract
{
    public function transform(DemandCompany $demand)
    {
        $demand->user;
        return $demand->toArray();
    }
}