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
}
