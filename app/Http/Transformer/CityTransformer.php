<?php
/**
 * city
 */
namespace App\Http\Transformer;

use App\Models\ChinaCity;
use League\Fractal\TransformerAbstract;

class CityTransformer extends TransformerAbstract
{
    public function transform(ChinaCity $city)
    {
        return [
            'oid' => (int)$city->oid,
            'name' => (string)$city->name,
            'pid' => (int)$city->pid,
            'sort' => (int)$city->sort,
        ];
    }
}