<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function scopeWithSalesCount($query)
    {
        return $query->withCount('sales as total');
    }

    public function getLabel(): string
    {
        return $this->nombre ?? '';
    }

    public function customers()
    {
        return $this->hasMany(Customers::class, 'id_entidad');
    }

    // HasManyThrough to easily count sales from an entity type
    public function sales()
    {
        return $this->hasManyThrough(Sales::class, Customers::class, 'id_entidad', 'id_vendedor');
    }
}
