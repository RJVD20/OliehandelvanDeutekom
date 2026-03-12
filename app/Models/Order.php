<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'name',
        'email',
        'address',
        'postcode',
        'city',
        'province',
        'route_date',
        'route_sequence',
        'route_travel_minutes',
        'route_stop_minutes',
        'route_notes',
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

    /**
     * Create an order and its items from a shopping cart session array.
     *
     * @param  array  $cart  ['productId' => ['name', 'price', 'quantity'], ...]
     * @param  array  $customerData  Validated customer fields (name, email, address, …)
     */
    public static function createFromCart(array $cart, array $customerData): self
    {
        $total = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

        $order = self::create(array_merge($customerData, [
            'status' => OrderStatus::PENDING,
            'total'  => $total,
        ]));

        foreach ($cart as $productId => $item) {
            $order->items()->create([
                'product_id'   => $productId,
                'product_name' => $item['name'],
                'price'        => $item['price'],
                'quantity'     => $item['quantity'],
            ]);
        }

        return $order;
    }

    /**
     * Duplicate this order as a new pending order (re-order).
     */
    public function duplicate(): self
    {
        $new = self::create([
            'user_id'  => $this->user_id,
            'status'   => OrderStatus::PENDING,
            'total'    => $this->total,
            'name'     => $this->name,
            'email'    => $this->email,
            'address'  => $this->address,
            'postcode' => $this->postcode,
            'city'     => $this->city,
            'province' => $this->province,
        ]);

        foreach ($this->items as $item) {
            $new->items()->create([
                'product_id'   => $item->product_id,
                'product_name' => $item->product_name,
                'price'        => $item->price,
                'quantity'     => $item->quantity,
            ]);
        }

        return $new;
    }
}
