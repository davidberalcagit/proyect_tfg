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
        'id_estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // Scopes
    public function scopeOverlapping($query, $carId, $start, $end)
    {
        return $query->where('id_vehiculo', $carId)
                     ->whereIn('id_estado', [1, 2, 3, 7]) // Activos/Pendientes
                     ->where(function ($q) use ($start, $end) {
                         $q->whereBetween('fecha_inicio', [$start, $end])
                           ->orWhereBetween('fecha_fin', [$start, $end])
                           ->orWhere(function ($q2) use ($start, $end) {
                               $q2->where('fecha_inicio', '<=', $start)
                                  ->where('fecha_fin', '>=', $end);
                           });
                     });
    }

    // Nuevo Scope 10: Activos (Usando)
    public function scopeActive($query)
    {
        return $query->where('id_estado', 3);
    }

    // Relaciones
    public function car()
    {
        return $this->belongsTo(Cars::class, 'id_vehiculo');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'id_cliente');
    }

    public function status()
    {
        return $this->belongsTo(RentalStatus::class, 'id_estado');
    }
}
