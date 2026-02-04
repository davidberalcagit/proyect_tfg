<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarStatus extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'nombre'];

    public function getLabel(): string
    {
        return $this->nombre ?? '';
    }
}
