<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Individuals extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','nombre','apellidos','telefono','dni','correo'];
}
