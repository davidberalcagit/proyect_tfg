<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModels extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_marca',
        'nombre',
        ];

    public function cars()
    {
        return $this->hasMany(Cars::class, 'id_modelo');
    }
    public function brand()
    {
        return $this->belongsTo(Brands::class, 'id_marca');
    }

}
