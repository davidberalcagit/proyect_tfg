<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_vehiculo',
        'id_comprador',
        'id_vendedor',
        'cantidad',
        'estado',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('estado', 'pending');
    }

    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('id_vendedor', $sellerId)
                     ->with(['car', 'buyer'])
                     ->latest();
    }

    // Relaciones
    public function car()
    {
        return $this->belongsTo(Cars::class, 'id_vehiculo');
    }

    public function buyer()
    {
        return $this->belongsTo(Customers::class, 'id_comprador');
    }

    public function seller()
    {
        return $this->belongsTo(Customers::class, 'id_vendedor');
    }
}
