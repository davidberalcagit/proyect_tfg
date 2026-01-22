<?php

namespace App\Models;
use Database\Seeders\GearSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'id_vendedor',
        'id_marcha',
        'id_modelo',
        'id_marca',
        'id_combustible',
        'matricula',
        'anyo_matri',
        'id_color',
        'km',
        'precio',
        'descripcion',
        'image',
        'id_estado',
        'rejection_reason',
        'id_listing_type',
        'temp_brand',
        'temp_model',
        'temp_color',
    ];

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->whereIn('id_estado', [1, 3]);
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('id_vendedor', $sellerId);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['brand'] ?? null, function ($q, $brand) {
            $q->where('id_marca', $brand);
        })
        ->when($filters['min_price'] ?? null, function ($q, $price) {
            $q->where('precio', '>=', $price);
        })
        ->when($filters['max_price'] ?? null, function ($q, $price) {
            $q->where('precio', '<=', $price);
        });
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('descripcion', 'like', "%{$term}%")
              ->orWhereHas('marca', fn($q2) => $q2->where('nombre', 'like', "%{$term}%"));
        });
    }

    // Relaciones
    public function vendedor(){
        return $this->belongsTo(Customers::class, 'id_vendedor');
    }

    public function sales(){
        return $this->belongsTo(Sales::class, 'id_vehiculo');
    }

    public function rentals(){
        return $this->hasMany(Rental::class, 'id_vehiculo');
    }

    public function renters()
    {
        return $this->belongsToMany(Customers::class, 'rentals', 'id_vehiculo', 'id_cliente')
                    ->withPivot('fecha_inicio', 'fecha_fin', 'precio_total', 'id_estado')
                    ->withTimestamps();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'car_id', 'user_id')->withTimestamps();
    }

    public function marcha(){
        return $this->belongsTo(Gears::class, 'id_marcha');
    }

    public function combustible(){
        return $this->belongsTo(Fuels::class, 'id_combustible');
    }

    public function color(){
        return $this->belongsTo(Color::class, 'id_color');
    }

    public function marca()
    {
        return $this->belongsTo(Brands::class, 'id_marca');
    }

    public function modelo()
    {
        return $this->belongsTo(CarModels::class, 'id_modelo');
    }

    public function status()
    {
        return $this->belongsTo(CarStatus::class, 'id_estado');
    }

    public function listingType()
    {
        return $this->belongsTo(ListingType::class, 'id_listing_type');
    }
}
