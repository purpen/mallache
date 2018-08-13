<?php

namespace App\Models;


class DesignTarget extends BaseModel
{
    protected $table = 'design_targets';

    protected $fillable = ['design_company_id', 'count' , 'turnover' , 'year'];

}
