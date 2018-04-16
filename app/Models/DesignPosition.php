<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignPosition extends Model
{
    protected $table = 'design_position';

    public function info()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
