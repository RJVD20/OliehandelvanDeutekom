<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'source',
        'total',
        'name',
        'email',
        'phone',
        'address',
        'postcode',
        'city',
        'province',
        'route_date',
        'route_sequence',
        'route_travel_minutes',
        'route_stop_minutes',
        'route_notes',
        'assigned_admin_id',
        'delivery_route_id',
        'geo_lat',
        'geo_lng',
        'geo_address_hash',
    ];

    protected $casts = [
        'route_date' => 'date',
        'status'     => OrderStatus::class,
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class, 'delivery_route_id');
    }

    /**
     * Create an order and its immutable line-item snapshots from the session cart.
     */
    public static function createFromCart(array $cart, array $customer): self
    {
        if ($cart === []) {
            throw new InvalidArgumentException('Kan geen bestelling maken van een lege winkelmand.');
        }

        return DB::transaction(function () use ($cart, $customer) {
            $items = collect($cart)->map(function (array $item, int|string $productId) {
                $quantity = max(1, (int) ($item['quantity'] ?? 0));
                $price = round((float) ($item['price'] ?? 0), 2);

                if ($price < 0 || empty($item['name'])) {
                    throw new InvalidArgumentException('De winkelmand bevat ongeldige productgegevens.');
                }

                return [
                    'product_id' => (int) $productId,
                    'product_name' => (string) $item['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                ];
            })->values();

            $order = static::create([
                ...$customer,
                'status' => OrderStatus::PENDING,
                'total' => round($items->sum(
                    fn (array $item) => $item['price'] * $item['quantity']
                ), 2),
            ]);

            $order->items()->createMany($items->all());

            return $order->load('items');
        });
    }

    /**
     * Create a fresh pending order with snapshots of the same products.
     */
    public function duplicate(): self
    {
        return DB::transaction(function () {
            $copy = $this->replicate([
                'status',
                'route_date',
                'route_sequence',
                'route_travel_minutes',
                'route_stop_minutes',
                'route_notes',
                'assigned_admin_id',
                'delivery_route_id',
                'geo_lat',
                'geo_lng',
                'geo_address_hash',
            ]);

            $copy->status = OrderStatus::PENDING;
            $copy->save();

            $copy->items()->createMany(
                $this->items->map(fn (OrderItem $item) => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ])->all()
            );

            return $copy->load('items');
        });
    }
}
