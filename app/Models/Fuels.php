<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fuels extends Model
{
    use HasFactory;
    protected $table = 'fuels';
    public function cars()
    {
        return $this->hasMany(Cars::class,'id_combustible');
    }
}
