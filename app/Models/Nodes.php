<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nodes extends Model
{
    protected $table = 'nodes';

    protected $fillable = ['name'];

    public function info()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
        ];
    }
}
