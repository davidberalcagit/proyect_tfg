<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_vehiculo',
        'precio'
    ];

    public function vehiculo(){
        return $this->belongsTo(Cars::class, 'id_vehiculo');
    }
}
