<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{
    protected $fillable = [
        'name',
        'route_date',
        'province',
        'admin_id',
        'notes',
    ];

    protected $casts = [
        'route_date' => 'date',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_route_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
