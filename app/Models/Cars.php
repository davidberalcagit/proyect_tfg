<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_vendedor',
        'marca',
        'modelo',
        'matricula',
        'año_matri',
        'motor',
        'combustible',
        'cambio',
        'color',
        'km',
        'precio',
        'moto',
        'foto',
        'descripcion',
    ];
    public function vendedor(){
        return $this->belongsTo(Sellers::class, 'id_vendedor');
    }
    public function sales(){
        return $this->belongsTo(Sales::class, 'id_vehiculo');
    }

}
