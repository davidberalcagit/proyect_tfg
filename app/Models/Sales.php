<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'id_comprador',
        'id_vendedor',
        'id_vehiculo',
    ];
    public function comprador(){
        return $this->belongsTo(Buyers::class, 'id_comprador');
    }
    public function vendedor(){
        return $this->belongsTo(Sellers::class, 'id_vendedor');
    }
    public function vehiculo(){
        return $this->belongsTo(Cars::class, 'id_vehiculo');
    }
}
