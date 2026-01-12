<?php

namespace App\Models;
use Database\Seeders\GearSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'id_vendedor',
        'id_marcha',
        'id_modelo',
        'id_marca',
        'id_combustible',
        'matricula',
        'anyo_matri',
        'id_color',
        'km',
        'precio',
        'descripcion',
        'image',
        'id_estado', // Changed from estado
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

    public function color(){
        return $this->belongsTo(Color::class, 'id_color');
    }

    public function marca()
    {
        return $this->belongsTo(Brands::class, 'id_marca');
    }

    public function modelo()
    {
        return $this->belongsTo(CarModels::class, 'id_modelo');
    }

    public function status()
    {
        return $this->belongsTo(CarStatus::class, 'id_estado');
    }
}
