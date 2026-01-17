<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_vehiculo',
        'id_cliente',
        'fecha_inicio',
        'fecha_fin',
        'precio_total',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function car()
    {
        return $this->belongsTo(Cars::class, 'id_vehiculo');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'id_cliente');
    }
}
