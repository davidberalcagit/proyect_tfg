<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ventas extends Model
{
    protected $fillable = [
        'id_comprador',
        'id_vendedor',
        'id_vehiculo',
    ];
    public function comprador(){
        return $this->belongsTo(Compradores::class, 'id_comprador');
    }
    public function vendedor(){
        return $this->belongsTo(Vendedores::class, 'id_vendedor');
    }
    public function vehiculo(){
        return $this->belongsTo(Vehiculos::class, 'id_vehiculo');
    }
}
