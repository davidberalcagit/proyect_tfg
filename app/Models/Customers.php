<?php

namespace App\Models;

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'id_entidad',
        'nombre_contacto',
        'telefono',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function entityType()
    {
        return $this->belongsTo(EntityType::class, 'id_entidad');
    }
    public function individuals()
    {
        return $this->hasOne(Individuals::class,'id_cliente');
    }
    public function dealerships()
    {
        return $this->hasOne(Dealerships::class,'id_cliente');
    }
    public function Cars()
    {
        return $this->hasMany(Cars::class,'id_vendedor');
    }
}
