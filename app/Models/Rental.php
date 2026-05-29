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

    public function scopeOverlapping($query, $carId, $start, $end)
    {
        $query->where('id_vehiculo', $carId);
        $query->whereIn('id_estado', [1, 2, 3, 7]);
        $query->where(function ($dateQuery) use ($start, $end) {
            $dateQuery->whereBetween('fecha_inicio', [$start, $end]);
            $dateQuery->orWhereBetween('fecha_fin', [$start, $end]);
            $dateQuery->orWhere(function ($envelopQuery) use ($start, $end) {
                $envelopQuery->where('fecha_inicio', '<=', $start);
                $envelopQuery->where('fecha_fin', '>=', $end);
            });

        });

        return $query;
    }


    public function scopeActive($query)
    {
        return $query->where('id_estado', 3);
    }

    public function getDurationDaysAttribute()
    {
        $days = $this->fecha_inicio->diffInDays($this->fecha_fin);
        return $days == 0 ? 1 : $days;
    }

    public function getServiceFeeAttribute()
    {
        return $this->precio_total * 0.15;
    }

    public function getTaxAttribute()
    {
        return $this->service_fee * 0.21;
    }

    public function getGrandTotalAttribute()
    {
        return $this->precio_total + $this->service_fee + $this->tax;
    }

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
