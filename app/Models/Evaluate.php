<?php
namespace App\Models;

class Evaluate extends BaseModel
{
    protected $table = 'evaluate';

    protected $fillable = [
        'demand_company_id',
        'design_company_id',
        'item_id',
        'content',
        'score',
    ];
}