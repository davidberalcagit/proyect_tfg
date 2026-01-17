<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;
    // Corregido: 'marca' cambiado a 'nombre' para coincidir con la DB y el controlador
    protected $fillable=['nombre'];

    public function models()
    {
        return $this->hasMany(CarModels::class, 'id_marca');
    }
}
