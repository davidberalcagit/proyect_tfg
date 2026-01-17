<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function cars()
    {
        return $this->hasMany(Cars::class, 'id_listing_type');
    }
}
