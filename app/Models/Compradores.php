<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compradores extends Model
{
    protected $fillable = [
        'id_particular',
        'id_empresa',
    ];
    public function particular(){
        return $this->belongsTo(Particulares::class, 'id_particular');
    }
    public function empresa(){
        return $this->belongsTo(Empresas::class, 'id_empresa');
    }
}
