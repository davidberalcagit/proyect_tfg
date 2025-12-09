<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Individuals extends Model
{
    protected $fillable = [
        'id_cliente',
        'dni',
        'fecha_nacimiento',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class,'id_cliente');
    }
}
