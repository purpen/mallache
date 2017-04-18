<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRecommend extends Model
{
    protected $table = 'item_recommend';

    protected $fillable = [
        'item_id',
        'design_company_id',
    ];

}
