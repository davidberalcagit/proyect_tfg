<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function getLabel(): string
    {
        return $this->nombre ?? '';
    }
}
