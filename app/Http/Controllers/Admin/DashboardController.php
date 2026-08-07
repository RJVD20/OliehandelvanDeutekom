<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DeliveryRoute;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $placedOrders = Order::query()->placed();
        $cashOpen = Payment::query()->where('status', PaymentStatus::OPEN)->where('meta->handling', 'cash_on_delivery')->count();
        $overduePayments = Payment::query()->where('status', PaymentStatus::OPEN)->whereDate('due_date', '<', today())
            ->where(fn ($query) => $query->whereNull('meta->handling')->orWhereNotIn('meta->handling', ['pay_on_delivery', 'cash_on_delivery']))->count();
        $promotionsEnding = Promotion::query()->currentlyActive()->whereBetween('ends_at', [now(), now()->addDays(7)])->count();

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
            'attentionItems' => [
                ['label' => 'Contante betalingen nog af te vinken', 'count' => $cashOpen, 'route' => route('admin.payments.index', ['status' => 'open', 'handling' => 'cash_on_delivery']), 'tone' => 'amber'],
                ['label' => 'Bezorgingen nog in te plannen', 'count' => (clone $placedOrders)->where('fulfillment_method', 'delivery')->whereNull('delivery_route_id')->where('status', OrderStatus::PENDING)->count(), 'route' => route('admin.routes.smart'), 'tone' => 'purple'],
                ['label' => 'Betalingen over de vervaldatum', 'count' => $overduePayments, 'route' => route('admin.payments.index', ['status' => 'open', 'due_before' => today()->subDay()->toDateString()]), 'tone' => 'red'],
                ['label' => 'Acties die binnen 7 dagen aflopen', 'count' => $promotionsEnding, 'route' => route('admin.promotions.index'), 'tone' => 'blue'],
            ],
        ]);
    }
}
