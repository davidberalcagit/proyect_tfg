<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    use HasFactory;

    protected $fillable = ['nombre']; // Corregido de 'name' a 'nombre'

    public function customers()
    {
        return $this->hasMany(Customers::class, 'id_entidad');
    }
}
