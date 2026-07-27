<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\User;
use App\Services\SmartRoutePlanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SmartRouteController extends Controller
{
    public function index(): View
    {
        $orders = $this->unplannedOrders()->get();

        return view('admin.routes.smart', [
            'orders' => $orders,
            'admins' => $this->admins(),
            'provinces' => nl_provinces(),
            'proposal' => null,
            'routeData' => [
                'route_date' => now()->addDay()->toDateString(),
                'name' => 'Bezorgroute '.now()->addDay()->format('d-m-Y'),
                'province' => null,
                'admin_user_id' => null,
            ],
            'mapboxToken' => config('services.mapbox.token'),
        ]);
    }

    public function preview(Request $request, SmartRoutePlanner $planner): View|RedirectResponse
    {
        $data = $request->validate($this->proposalRules());
        $orders = $this->unplannedOrders()
            ->whereIn('id', $data['order_ids'])
            ->get();

        if ($orders->count() !== count(array_unique($data['order_ids']))) {
            return back()->withInput()->withErrors([
                'order_ids' => 'Een of meer bestellingen zijn ondertussen al ingepland. Vernieuw de selectie.',
            ]);
        }

        return view('admin.routes.smart', [
            'orders' => $this->unplannedOrders()->get(),
            'admins' => $this->admins(),
            'provinces' => nl_provinces(),
            'proposal' => $planner->createProposal($orders),
            'routeData' => $data,
            'mapboxToken' => config('services.mapbox.token'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            ...$this->proposalRules(),
            'order_ids' => ['required', 'array', 'min:1', 'max:25'],
            'order_ids.*' => ['required', 'integer', 'distinct', 'exists:orders,id'],
        ]);

        $route = DB::transaction(function () use ($data) {
            $orders = $this->unplannedOrders()
                ->lockForUpdate()
                ->whereIn('id', $data['order_ids'])
                ->get()
                ->keyBy('id');

            if ($orders->count() !== count($data['order_ids'])) {
                abort(409, 'Een of meer bestellingen zijn ondertussen al ingepland.');
            }

            $route = DeliveryRoute::create([
                'route_date' => $data['route_date'],
                'name' => $data['name'],
                'province' => $data['province'] ?? null,
                'admin_id' => $data['admin_user_id'] ?? null,
            ]);

            foreach ($data['order_ids'] as $index => $orderId) {
                $orders->get($orderId)->update([
                    'delivery_route_id' => $route->id,
                    'assigned_admin_id' => $route->admin_id,
                    'route_date' => $route->route_date,
                    'route_sequence' => $index + 1,
                    'route_stop_minutes' => 10,
                ]);
            }

            return $route;
        });

        return redirect()->route('admin.routes.index', [
            'route_date' => $route->route_date->toDateString(),
            'route_id' => $route->id,
        ])->with('toast', "Route '{$route->name}' met ".count($data['order_ids']).' stops is bevestigd.');
    }

    private function proposalRules(): array
    {
        return [
            'route_date' => ['required', 'date', 'after_or_equal:today'],
            'name' => ['required', 'string', 'max:255'],
            'province' => ['nullable', Rule::in(nl_provinces())],
            'admin_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('is_admin', true),
            ],
            'order_ids' => ['required', 'array', 'min:1', 'max:25'],
            'order_ids.*' => ['required', 'integer', 'distinct', 'exists:orders,id'],
        ];
    }

    private function unplannedOrders()
    {
        return Order::query()
            ->placed()
            ->with(['items', 'latestPayment'])
            ->whereNull('delivery_route_id')
            ->whereNull('route_date')
            ->where('fulfillment_method', 'delivery')
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->orderBy('province')
            ->orderBy('postcode')
            ->orderBy('created_at');
    }

    private function admins()
    {
        return User::query()->where('is_admin', true)->orderBy('name')->get();
    }
}
