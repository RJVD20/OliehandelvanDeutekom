<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'category_id',
        'type',
        'brand',
        'model_type',
        'used',
        'rate_group',
        'description',
        'short_description',
        'specifications',
        'image',
        'active',
        'featured',
    ];

    protected $casts = [
        'active' => 'boolean',
        'used'   => 'boolean',
        'specifications' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'main_product_id');
    }
}
