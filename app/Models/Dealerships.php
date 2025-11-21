<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealerships extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'nombre',
        'telefono',
        'nif',
        'correo',
        'direccion'];
    public function users(){
        return $this->belongsTo(User::class);
    }
}
