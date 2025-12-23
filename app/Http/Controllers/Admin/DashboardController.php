<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalProducts'    => Product::count(),
            'activeProducts'   => Product::where('active', true)->count(),
            'inactiveProducts' => Product::where('active', false)->count(),
            'totalOrders'      => Order::count(),
            'recentOrders'     => Order::latest()->take(5)->get(),
        ]);
    }
}
