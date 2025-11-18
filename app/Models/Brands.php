<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    public function marca()
    {
        return $this->belongsTo(Models::class, 'id_marca');
    }
}
