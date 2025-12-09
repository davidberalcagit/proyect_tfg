<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gears extends Model
{
    use HasFactory;
    protected $table = 'gears';
    protected $fillable = ['tipo'];

    public function car(){
        return $this->hasMany(Cars::class,'id_marcha');
    }
}
