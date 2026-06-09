<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;
    protected $fillable=['nombre'];

    public function scopeMostPopular($query)
    {
        return $query->withCount('sales as cars_sales_count')
                     ->orderByDesc('cars_sales_count');
    }

    public function models()
    {
        return $this->hasMany(CarModels::class, 'id_marca');
    }

    public function cars()
    {
        return $this->hasMany(Cars::class, 'id_marca');
    }

    public function sales()
    {
        return $this->hasManyThrough(Sales::class, Cars::class, 'id_marca', 'id_vehiculo');
    }
}
