<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculos extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_vendedor',
        'marca',
        'modelo',
        'matricula',
        'año_matri',
        'motor',
        'combustible',
        'cambio',
        'color',
        'km',
        'precio',
        'moto',
        'foto',
        'descripcion',
    ];
    public function vendedor(){
        return $this->belongsTo(Vendedores::class, 'id_vendedor');
    }
    public function ventas(){
        return $this->belongsTo(Ventas::class, 'id_vehiculo');
    }

}
