<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gears extends Model
{
    use HasFactory;

    protected $fillable = ['tipo'];

    public function getLabel(): string
    {
        return $this->tipo ?? '';
    }
}
