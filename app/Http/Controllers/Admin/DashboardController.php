<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DeliveryRoute;
use App\Models\Product;
use App\Models\Order;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $placedOrders = Order::query()->placed();

        return view('admin.dashboard', [
            'ordersToday' => (clone $placedOrders)->whereDate('created_at', today())->count(),
            'revenueThisMonth' => (clone $placedOrders)
                ->whereHas('latestPayment', fn ($payment) => $payment->where('status', PaymentStatus::PAID))
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total'),
            'openPayments' => (clone $placedOrders)
                ->whereHas('latestPayment', fn ($payment) => $payment->where('status', PaymentStatus::OPEN))
                ->count(),
            'unplannedDeliveries' => (clone $placedOrders)
                ->where('fulfillment_method', 'delivery')
                ->whereNull('delivery_route_id')
                ->where('status', OrderStatus::PENDING)
                ->count(),
            'activeProducts' => Product::where('active', true)->count(),
            'inactiveProducts' => Product::where('active', false)->count(),
            'recentOrders' => Order::query()
                ->placed()
                ->with('latestPayment')
                ->withSum('items as item_quantity', 'quantity')
                ->latest()
                ->take(6)
                ->get(),
            'upcomingRoutes' => DeliveryRoute::query()
                ->with('admin')
                ->withCount('orders')
                ->whereDate('route_date', '>=', today())
                ->orderBy('route_date')
                ->take(4)
                ->get(),
            'recentAuditLogs' => AuditLog::query()
                ->with('user')
                ->latest()
                ->take(5)
                ->get(),
            'maintenanceEnabled' => Setting::getBool('maintenance_enabled', false),
        ]);
    }
}
