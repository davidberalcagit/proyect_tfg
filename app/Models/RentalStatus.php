<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalStatus extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'id_estado');
    }
}
