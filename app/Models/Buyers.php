<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyers extends Model
{
    protected $fillable = [
        'id_particular',
        'id_empresa',
    ];
    public function particular(){
        return $this->belongsTo(Individuals::class, 'id_particular');
    }
    public function empresa(){
        return $this->belongsTo(Dealerships::class, 'id_empresa');
    }
}
