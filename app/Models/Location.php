<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'street',
        'postcode_city',
        'opening',
        'phone',
        'lat',
        'lng',
        'show_on_map',
        'remark',
    ];
}
