<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sellers extends Model
{
    /** @use HasFactory<\Database\Factories\SellersFactory> */
    use HasFactory;
    protected $fillable = [
        'id_particular',
        'id_empresa',
    ];
    public function indiindividual()
    {
        return $this->belongsTo(Individuals::class, 'id_cliente');
    }

    public function empresa()
    {
        return $this->belongsTo(Dealerships::class, 'id_empresa');
    }

    public function cars()
    {
        return $this->hasMany(Cars::class, 'id_vendedor');
    }
}
