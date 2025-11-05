<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendedores extends Model
{
    /** @use HasFactory<\Database\Factories\VendedoresFactory> */
    use HasFactory;
    protected $fillable = [
        'id_particular',
        'id_empresa',
    ];
    public function particular()
    {
        return $this->belongsTo(Particulares::class, 'id_cliente');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresas::class, 'id_empresa');
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculos::class, 'id_vendedor');
    }
}
