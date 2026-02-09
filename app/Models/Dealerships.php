<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealerships extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre_empresa',
        'nif',
        'direccion',
    ];

    public function customers(){
        return $this->hasMany(Customers::class, 'dealership_id');
    }
}
