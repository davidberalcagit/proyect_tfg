<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'id_entidad',
        'dealership_id',
        'nombre_contacto',
        'telefono',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function entityType()
    {
        return $this->belongsTo(EntityType::class, 'id_entidad');
    }
    public function individual()
    {
        return $this->hasOne(Individuals::class,'id_cliente');
    }

    public function dealership()
    {
        return $this->belongsTo(Dealerships::class, 'dealership_id');
    }

    public function cars()
    {
        return $this->hasMany(Cars::class,'id_vendedor');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'id_cliente');
    }

    // Relación N:N con Cars a través de rentals
    public function rentedCars()
    {
        return $this->belongsToMany(Cars::class, 'rentals', 'id_cliente', 'id_vehiculo')
                    ->withPivot('fecha_inicio', 'fecha_fin', 'precio_total', 'id_estado')
                    ->withTimestamps();
    }

    // Relación N:N con Cars a través de offers (bidCars)
    public function bidCars()
    {
        return $this->belongsToMany(Cars::class, 'offers', 'id_comprador', 'id_vehiculo')
                    ->withPivot('cantidad', 'estado', 'id_vendedor')
                    ->withTimestamps();
    }
}
