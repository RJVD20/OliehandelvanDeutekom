<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


public function index()
{
    return view('admin.products.index', [
        'products'         => Product::latest()->paginate(20),
        'totalProducts'    => Product::count(),
        'activeProducts'   => Product::where('active', true)->count(),
        'inactiveProducts' => Product::where('active', false)->count(),
        'totalOrders'      => Order::count(),
    ]);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
