<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRoute;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use App\Services\RouteOptimizationUsage;
use App\Services\SmartRoutePlanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SmartRouteController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $this->unplannedOrders()->get();

        return view('admin.routes.smart', [
            'orders' => $orders,
            'admins' => $this->admins(),
            'locations' => $this->locations(),
            'provinces' => nl_provinces(),
            'proposal' => null,
            'routeData' => [
                'route_date' => now()->addDay()->toDateString(),
                'name' => 'Bezorgroute '.now()->addDay()->format('d-m-Y'),
                'province' => null,
                'admin_user_id' => null,
                'start_location_id' => null,
                'end_location_id' => null,
            ],
            'mapboxToken' => config('services.mapbox.token'),
            'googleUsage' => app(RouteOptimizationUsage::class)->summary(),
        ]);
    }

    public function preview(Request $request, SmartRoutePlanner $planner): View|RedirectResponse
    {
        $data = $request->validate($this->proposalRules(), $this->routeValidationMessages());
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
            'locations' => $this->locations(),
            'provinces' => nl_provinces(),
            'proposal' => $planner->createProposal(
                $orders,
                ! empty($data['start_location_id']) ? Location::find($data['start_location_id']) : null,
                ! empty($data['end_location_id']) ? Location::find($data['end_location_id']) : null,
            ),
            'routeData' => $data,
            'mapboxToken' => config('services.mapbox.token'),
            'googleUsage' => app(RouteOptimizationUsage::class)->summary(),
        ]);
    }

    public function manage(Request $request): View
    {
        $managementData = $this->managementData($request);

        return view('admin.routes.manage', [
            ...$managementData,
            'admins' => $this->admins(),
            'mapboxToken' => config('services.mapbox.token'),
            // The map requests its route line after the page has rendered. Waiting
            // for Mapbox here made switching routes block on an external API call.
            'routeGeometry' => null,
        ]);
    }

    public function loading(DeliveryRoute $deliveryRoute): View
    {
        $inventory = $this->loadingInventory($deliveryRoute);
        $checkedItems = collect($deliveryRoute->loading_checked_items ?? [])
            ->intersect($inventory->pluck('key'))
            ->values();

        return view('admin.routes.loading', [
            'deliveryRoute' => $deliveryRoute,
            'inventory' => $inventory,
            'checkedItems' => $checkedItems,
            'totalQuantity' => $inventory->sum('quantity'),
            'loadedQuantity' => $inventory
                ->whereIn('key', $checkedItems)
                ->sum('quantity'),
        ]);
    }

    public function toggleLoadingItem(Request $request, DeliveryRoute $deliveryRoute): JsonResponse|RedirectResponse
    {
        $inventory = $this->loadingInventory($deliveryRoute);
        $data = $request->validate([
            'item_key' => ['required', 'string', Rule::in($inventory->pluck('key')->all())],
            'loaded' => ['required', 'boolean'],
        ]);

        $checkedItems = collect($deliveryRoute->loading_checked_items ?? [])
            ->intersect($inventory->pluck('key'));

        if ($data['loaded']) {
            $checkedItems->push($data['item_key']);
        } else {
            $checkedItems = $checkedItems->reject(fn (string $key) => $key === $data['item_key']);
        }

        $checkedItems = $checkedItems->unique()->values();
        $deliveryRoute->update(['loading_checked_items' => $checkedItems->all()]);

        $loadedQuantity = $inventory
            ->whereIn('key', $checkedItems)
            ->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'checked_items' => $checkedItems,
                'checked_count' => $checkedItems->count(),
                'total_count' => $inventory->count(),
                'loaded_quantity' => $loadedQuantity,
                'total_quantity' => $inventory->sum('quantity'),
            ]);
        }

        return back()->with('toast', 'Laadstatus bijgewerkt.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'google_routes_monthly_limit' => ['required', 'integer', 'min:1', 'max:5000'],
            'google_routes_alert_email' => ['required', 'email:rfc', 'max:255'],
        ]);

        \App\Models\Setting::set('google_routes_monthly_limit', $data['google_routes_monthly_limit']);
        \App\Models\Setting::set('google_routes_alert_email', $data['google_routes_alert_email']);

        return back()->with('toast', 'Google Routes-limiet opgeslagen.');
    }

    public function destroy(DeliveryRoute $deliveryRoute): RedirectResponse
    {
        $routeDate = $deliveryRoute->route_date->toDateString();
        $routeName = $deliveryRoute->name;

        DB::transaction(function () use ($deliveryRoute) {
            $deliveryRoute->orders()->update([
                'delivery_route_id' => null,
                'assigned_admin_id' => null,
                'route_date' => null,
                'route_sequence' => null,
                'route_travel_minutes' => null,
                'route_stop_minutes' => null,
                'route_notes' => null,
            ]);

            $deliveryRoute->delete();
        });

        return redirect()->route('admin.routes.index', [
            'route_date' => $routeDate,
        ])->with('toast', "Route '{$routeName}' verwijderd. De gekoppelde bestellingen zijn weer ongepland.");
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            ...$this->proposalRules(),
            'order_ids' => ['required', 'array', 'min:1', 'max:25'],
            'order_ids.*' => ['required', 'integer', 'distinct', 'exists:orders,id'],
        ], $this->routeValidationMessages());

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
                'start_location_id' => $data['start_location_id'] ?? null,
                'end_location_id' => $data['end_location_id'] ?? null,
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
            'name' => ['required', 'string', 'max:255', Rule::unique('delivery_routes', 'name')],
            'province' => ['nullable', Rule::in(nl_provinces())],
            'admin_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('is_admin', true),
            ],
            'start_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'end_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'order_ids' => ['required', 'array', 'min:1', 'max:25'],
            'order_ids.*' => ['required', 'integer', 'distinct', 'exists:orders,id'],
        ];
    }

    private function routeValidationMessages(): array
    {
        return [
            'name.unique' => 'Deze routenaam bestaat al. Kies een andere routenaam.',
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

    private function locations()
    {
        return Location::query()->orderBy('name')->get();
    }

    private function managementData(Request $request): array
    {
        $routeDate = $request->input('route_date', now()->toDateString());

        if (! is_string($routeDate) || ! strtotime($routeDate)) {
            $routeDate = now()->toDateString();
        }

        $deliveryRoutes = DeliveryRoute::query()
            ->whereDate('route_date', $routeDate)
            ->with(['admin', 'startLocation', 'endLocation'])
            ->withCount('orders')
            ->orderBy('name')
            ->get();

        $selectedDeliveryRoute = $request->filled('route_id')
            ? $deliveryRoutes->firstWhere('id', (int) $request->input('route_id'))
            : null;
        $selectedDeliveryRoute ??= $deliveryRoutes->first();

        $existingRouteOrders = $selectedDeliveryRoute
            ? $selectedDeliveryRoute->orders()
                ->orderByRaw('route_sequence IS NULL')
                ->orderBy('route_sequence')
                ->orderBy('id')
                ->get()
            : collect();

        return compact(
            'routeDate',
            'deliveryRoutes',
            'selectedDeliveryRoute',
            'existingRouteOrders',
        );
    }

    private function loadingInventory(DeliveryRoute $deliveryRoute)
    {
        $deliveryRoute->load([
            'admin',
            'orders' => fn ($query) => $query
                ->orderByRaw('route_sequence IS NULL')
                ->orderBy('route_sequence')
                ->orderBy('id'),
            'orders.items.product',
        ]);

        return $deliveryRoute->orders
            ->flatMap(fn (Order $order) => $order->items->map(fn ($item) => [
                'key' => 'product:'.$item->product_id,
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'image' => $item->product?->image,
                'quantity' => (int) $item->quantity,
                'stop' => $order->route_sequence,
                'order_id' => $order->id,
                'customer' => $order->name,
            ]))
            ->groupBy('key')
            ->map(function ($items, string $key) {
                $first = $items->first();

                return [
                    'key' => $key,
                    'product_id' => $first['product_id'],
                    'name' => $first['name'],
                    'image' => $first['image'],
                    'quantity' => $items->sum('quantity'),
                    'stops' => $items->map(fn (array $item) => [
                        'sequence' => $item['stop'],
                        'order_id' => $item['order_id'],
                        'customer' => $item['customer'],
                        'quantity' => $item['quantity'],
                    ])->values(),
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

}
