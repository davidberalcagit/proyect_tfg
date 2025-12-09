<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealerships extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_cliente',
        'nombre_empresa',
        'nif',
        'direccion',
    ];
    public function customer(){
        return $this->belongsTo(Customers::class,'id_cliente');
    }
}
