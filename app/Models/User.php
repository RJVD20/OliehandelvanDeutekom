<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Order;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'postcode',
        'city',
        'province',
        'phone',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Always store email in lowercase for case-insensitive auth.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => is_string($value) ? strtolower($value) : $value,
        );
    }

    /* ======================
       Relaties
    ====================== */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
