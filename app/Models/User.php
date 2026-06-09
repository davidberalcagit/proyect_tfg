<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeActiveTraders($query)
    {
        return $query->whereHas('customer.cars', function ($q) {
            $q->whereYear('created_at', now()->year);
        })->orWhereHas('customer.sales', function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        });
    }

    public function customer()
    {
        return $this->hasOne(Customers::class, 'id_usuario');
    }

    public function favorites()
    {
        return $this->belongsToMany(Cars::class, 'favorites', 'user_id', 'car_id')
                    ->withTimestamps();
    }
}
