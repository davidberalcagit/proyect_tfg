<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;
    protected $fillable=['nombre'];

    public function models()
    {
        return $this->hasMany(CarModels::class, 'id_marca');
    }
}
