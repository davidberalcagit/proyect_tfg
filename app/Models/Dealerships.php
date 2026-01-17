<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealerships extends Model
{
    use HasFactory;
    protected $fillable = [
        // 'id_cliente', // Eliminado
        'nombre_empresa',
        'nif',
        'direccion',
    ];

    // Relación cambiada: Un concesionario tiene muchos empleados (clientes)
    public function customers(){
        return $this->hasMany(Customers::class, 'dealership_id');
    }
}
