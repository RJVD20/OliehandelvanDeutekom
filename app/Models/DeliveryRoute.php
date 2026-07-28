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
        'start_location_id',
        'end_location_id',
        'notes',
        'loading_checked_items',
    ];

    protected $casts = [
        'route_date' => 'date',
        'loading_checked_items' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_route_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function startLocation()
    {
        return $this->belongsTo(Location::class, 'start_location_id');
    }

    public function endLocation()
    {
        return $this->belongsTo(Location::class, 'end_location_id');
    }
}
