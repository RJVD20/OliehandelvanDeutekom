<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'main_product_id', 'name', 'slug', 'title', 'short_description', 'description',
        'image_path', 'image_alt', 'fixed_price', 'free_shipping', 'show_home',
        'show_product', 'show_cart', 'active', 'starts_at', 'ends_at', 'sort_order',
    ];

    protected $casts = [
        'fixed_price' => 'decimal:2',
        'free_shipping' => 'boolean',
        'show_home' => 'boolean',
        'show_product' => 'boolean',
        'show_cart' => 'boolean',
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    public function items()
    {
        return $this->hasMany(PromotionItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        return $query->where('active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function isCurrentlyActive(): bool
    {
        return $this->active
            && (! $this->starts_at || $this->starts_at->lte(now()))
            && (! $this->ends_at || $this->ends_at->gte(now()));
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) return null;
        return str_starts_with($this->image_path, 'images/')
            ? asset($this->image_path)
            : asset('storage/'.$this->image_path);
    }

    public function normalValue(): float
    {
        return round((float) $this->mainProduct?->price + $this->items->sum(
            fn (PromotionItem $item) => (float) $item->product?->price * $item->quantity
        ), 2);
    }
}
