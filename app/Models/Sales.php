<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_vehiculo',
        'id_vendedor',
        'id_comprador',
        'precio',
        'id_estado'
    ];

    public function scopeMonthlyReport($query, $sellerId, $month, $year)
    {
        return $query->where('id_vendedor', $sellerId)
                     ->whereMonth('created_at', $month)
                     ->whereYear('created_at', $year)
                     ->where('id_estado', 1);
    }

    public function vehiculo(){
        return $this->belongsTo(Cars::class, 'id_vehiculo');
    }

    public function vendedor(){
        return $this->belongsTo(Customers::class, 'id_vendedor');
    }

    public function comprador(){
        return $this->belongsTo(Customers::class, 'id_comprador');
    }

    public function status()
    {
        return $this->belongsTo(SaleStatus::class, 'id_estado');
    }
}
