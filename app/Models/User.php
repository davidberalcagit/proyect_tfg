<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */


    public function scopeActiveTraders($query)
    {
        return $query->whereHas('customer.cars', function ($q) {
            $q->where('id_estado', 1);
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
