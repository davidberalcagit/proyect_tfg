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
        if (isset($filters['brand']) && $filters['brand'] !== null) {
            $query->where('id_marca', $filters['brand']);
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== null) {
            $query->where('precio', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== null) {
            $query->where('precio', '<=', $filters['max_price']);
        }

        return $query;
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($searchGroup) use ($term) {
            $searchGroup->where('title', 'like', "%{$term}%");
            $searchGroup->orWhere('descripcion', 'like', "%{$term}%");
            $searchGroup->orWhereHas('marca', function($brandQuery) use ($term) {
                $brandQuery->where('nombre', 'like', "%{$term}%");
            });

        });
    }

    public function scopeRecent($query, $days = 7)
    {
        $fechaLimite = now()->subDays($days);
        return $query->where('created_at', '>=', $fechaLimite);
    }

    public function scopeCheap($query, $maxPrice = 5000)
    {
        return $query->where('precio', '<=', $maxPrice);
    }

    public function vendedor(){
        return $this->belongsTo(Customers::class, 'id_vendedor');
    }

    public function sales(){
        return $this->hasOne(Sales::class, 'id_vehiculo');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'id_vehiculo');
    }

    public function bidders()
    {
        return $this->belongsToMany(Customers::class, 'offers', 'id_vehiculo', 'id_comprador')
                    ->withPivot('cantidad', 'estado', 'id_vendedor')
                    ->withTimestamps();
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
        return $this->belongsToMany(User::class, 'favorites', 'car_id', 'user_id')
                    ->withTimestamps();
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
