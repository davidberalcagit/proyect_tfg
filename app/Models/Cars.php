<?php

namespace App\Models;
use Database\Seeders\GearSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_vendedor',
        'id_marcha',
        'id_modelo',
        'id_marca',
        'id_combustible',
        'matricula',
        'año_matri',
        'motor',
        'combustible',
        'cambio',
        'color',
        'km',
        'precio',
        'foto',
        'descripcion',
    ];
    public function vendedor(){
        return $this->belongsTo(Customers::class, 'id_vendedor');
    }
    public function sales(){
        return $this->belongsTo(Sales::class, 'id_vehiculo');
    }
    public function marcha(){
        return $this->belongsTo(Gears::class, 'id_marcha');
    }
    public function combustible(){
        return $this->belongsTo(Fuels::class, 'id_combustible');
    }
}
