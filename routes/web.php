<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ManualOrderController;
use App\Http\Controllers\Admin\SmartRouteController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Services\ShippingRates;
use App\Services\CartPricing;
use App\Http\Controllers\NewsletterUnsubscribeController;

/*
|--------------------------------------------------------------------------
| Mail
|--------------------------------------------------------------------------
*/
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderShippedMail;


/*
|--------------------------------------------------------------------------
| Models
|--------------------------------------------------------------------------
*/
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\Promotion;
use App\Models\User;
use App\Models\DeliveryRoute;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use App\Services\Payments\PaymentService;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PromotionController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Publieke pagina’s
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Prefer featured products for the homepage slider; fallback to recent active products
    $products = Product::where('active', true)->where('featured', true)->take(8)->get();
    if ($products->isEmpty()) {
        $products = Product::where('active', true)->take(8)->get();
    }
    $categories = Category::all();
    $promotions = Promotion::currentlyActive()
        ->where('show_home', true)
        ->with(['mainProduct', 'items.product'])
        ->orderBy('sort_order')
        ->orderBy('id')
        ->limit(3)
        ->get();

    return view('themes.default.pages.home', compact('products', 'categories', 'promotions'));
})->name('home');

Route::get('/informatie', function () {
    return view('themes.default.pages.informatie');
})->name('informatie');

Route::get('/over-ons', function () {
    return view('themes.default.pages.over-ons');
})->name('over-ons');

Route::view('/privacy', 'themes.default.pages.legal', ['page' => 'privacy'])
    ->name('privacy');
Route::view('/algemene-voorwaarden', 'themes.default.pages.legal', ['page' => 'terms'])
    ->name('terms');
Route::view('/retourneren', 'themes.default.pages.legal', ['page' => 'returns'])
    ->name('returns');
Route::view('/cookies', 'themes.default.pages.legal', ['page' => 'cookies'])
    ->name('cookies');

Route::get('/locaties', function () {
    $locaties = Location::where('show_on_map', true)->orderBy('name')->get();
    return view('themes.default.pages.locaties', compact('locaties'));
})->name('locaties');

Route::redirect('/tarieven', '/vloeistoffen', 301)->name('tarieven');

/*
|--------------------------------------------------------------------------
| Chauffeur app
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/app', function (Request $request) {
        $routeDate = $request->input('route_date', now()->toDateString());

        $driverRoutes = DeliveryRoute::query()
            ->whereDate('route_date', $routeDate)
            ->where('admin_id', auth()->id())
            ->orderBy('name')
            ->get();

        $selectedRoute = null;
        if ($request->filled('route_id')) {
            $selectedRoute = $driverRoutes->firstWhere('id', (int) $request->input('route_id'));
        }
        if (! $selectedRoute) {
            $selectedRoute = $driverRoutes->first();
        }

        $orders = $selectedRoute
            ? Order::query()
                ->with('latestPayment')
                ->where('delivery_route_id', $selectedRoute->id)
                ->orderByRaw('route_sequence IS NULL')
                ->orderBy('route_sequence')
                ->orderBy('id')
                ->get()
            : collect();

        $routeMapUrl = null;
        if ($orders->count() >= 1) {
            $stops = $orders->map(function ($order) {
                return $order->address . ', ' . $order->postcode . ' ' . $order->city;
            })->values();

            $origin = 'Current Location';
            $destination = $stops->last();
            $waypoints = $stops->slice(0, max(0, $stops->count() - 1));

            $routeMapUrl = 'https://www.google.com/maps/dir/?api=1'
                . '&origin=' . urlencode($origin)
                . '&destination=' . urlencode($destination)
                . '&travelmode=driving';

            if ($waypoints->isNotEmpty()) {
                $routeMapUrl .= '&waypoints=' . urlencode($waypoints->implode('|'));
            }
        }

        return view('driver.app', compact('orders', 'routeDate', 'routeMapUrl', 'driverRoutes', 'selectedRoute'));
    })->name('driver.app');

    Route::post('/app/orders/{order}/complete', function (Order $order) {
        $assignedAdminId = $order->deliveryRoute?->admin_id;
        abort_unless((int) $assignedAdminId === (int) auth()->id(), 403);

        if ($order->latestPayment?->isCashPending()) {
            return back()->with('toast', 'Vink eerst aan dat het contante bedrag is ontvangen.');
        }

        $order->update(['status' => 'completed']);

        return back()->with('toast', 'Stop afgehandeld.');
    })->name('driver.orders.complete');

    Route::post('/app/orders/{order}/cash-received', function (Order $order) {
        $assignedAdminId = $order->deliveryRoute?->admin_id;
        abort_unless((int) $assignedAdminId === (int) auth()->id(), 403);

        $payment = $order->latestPayment;
        abort_unless($payment?->isCash(), 422);

        if ($payment->status === PaymentStatus::PAID) {
            return back()->with('toast', 'De contante betaling was al afgevinkt.');
        }

        $oldStatus = $payment->status;
        $payment->update([
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        PaymentEvent::create([
            'payment_id' => $payment->id,
            'type' => 'cash_received',
            'source' => 'driver',
            'actor_id' => auth()->id(),
            'data' => [
                'from' => $oldStatus->value,
                'to' => PaymentStatus::PAID->value,
                'amount' => $payment->amount,
            ],
        ]);

        return back()->with('toast', 'Contante betaling van € '.number_format($payment->amount, 2, ',', '.').' ontvangen.');
    })->name('driver.orders.cash-received');
});

/*
|--------------------------------------------------------------------------
| Producten (frontend)
|--------------------------------------------------------------------------
*/

Route::get('/product/{slug}', function ($slug, ShippingRates $rates) {
    $product = Product::with('category')->where('slug', $slug)->firstOrFail();
    $productRule = $rates->ruleForProduct($product->id);
    $promotion = Promotion::currentlyActive()
        ->where('show_product', true)
        ->where('main_product_id', $product->id)
        ->with(['items.product', 'mainProduct'])
        ->orderBy('sort_order')
        ->first();

    $suggestedProducts = Product::query()
        ->where('active', true)
        ->where('id', '!=', $product->id)
        ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
        ->inRandomOrder()
        ->limit(4)
        ->get();

    if ($suggestedProducts->count() < 4) {
        $remaining = 4 - $suggestedProducts->count();

        $moreProducts = Product::query()
            ->where('active', true)
            ->where('id', '!=', $product->id)
            ->whereNotIn('id', $suggestedProducts->pluck('id'))
            ->inRandomOrder()
            ->limit($remaining)
            ->get();

        $suggestedProducts = $suggestedProducts->concat($moreProducts)->values();
    }

    return view('themes.default.pages.product', compact('product', 'suggestedProducts', 'productRule', 'promotion'));
})->name('product.show');

Route::get('/categories/{slug}', function ($slug) {
    $category = Category::where('slug', $slug)->firstOrFail();
    $products = $category->products()->where('active', true)->paginate(12);

    return view('themes.default.pages.category', compact('category', 'products'));
})->name('category.show');



$productListing = function (Request $request, string $title, string $routeName, ?string $type = null) {
    $baseQuery = Product::query()->where('active', true);

    if ($type) {
        $baseQuery->where('type', $type);
    } else {
        $baseQuery->whereNotIn('type', ['kachel', 'vloeistof']);
    }

    $query = clone $baseQuery;

    if ($request->filled('categories')) {
        $query->whereIn('category_id', $request->categories);
    }

    if ($request->filled('brands')) {
        $query->whereIn('brand', $request->brands);
    }

    if ($request->filled('model_types')) {
        $query->whereIn('model_type', $request->model_types);
    }

    if ($request->get('condition') === 'used') {
        $query->where('used', true);
    } elseif ($request->get('condition') === 'new') {
        $query->where('used', false);
    }

    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    if ($request->filled('sort')) {
        match ($request->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest'     => $query->latest(),
            default      => null,
        };
    }

    $products   = $query->paginate(12)->withQueryString();
    $categories = Category::query()
        ->whereHas('products', function ($q) use ($type) {
            $q->where('active', true);
            $type ? $q->where('type', $type) : $q->whereNotIn('type', ['kachel', 'vloeistof']);
        })
        ->orderBy('name')
        ->get();

    $brands     = (clone $baseQuery)->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');
    $modelTypes = (clone $baseQuery)->whereNotNull('model_type')->distinct()->orderBy('model_type')->pluck('model_type');

    return view('themes.default.pages.products.index', compact(
        'products',
        'categories',
        'brands',
        'modelTypes',
        'title',
        'routeName',
        'type'
    ));
};

Route::get('/producten', fn (Request $request) => $productListing(
    $request,
    'Overige producten',
    'products.index'
))->name('products.index');

Route::get('/kachels', fn (Request $request) => $productListing(
    $request,
    'Kachels',
    'products.heaters',
    'kachel'
))->name('products.heaters');

Route::get('/vloeistoffen', fn (Request $request) => $productListing(
    $request,
    'Vloeistoffen',
    'products.liquids',
    'vloeistof'
))->name('products.liquids');

/*
|--------------------------------------------------------------------------
| Zoeken (suggesties)
|--------------------------------------------------------------------------
*/

Route::get('/search/suggest', function (Request $request) {
    $term = trim((string) $request->get('q', ''));

    if (strlen($term) < 2) {
        return response()->json([
            'categories' => [],
            'products'   => [],
        ]);
    }

    $categories = Category::query()
        ->where('name', 'like', "%{$term}%")
        ->orderBy('name')
        ->limit(6)
        ->get(['id', 'name', 'slug']);

    $products = Product::query()
        ->where('active', true)
        ->where('name', 'like', "%{$term}%")
        ->with('category:id,name')
        ->orderBy('name')
        ->limit(8)
        ->get(['id', 'name', 'slug', 'price', 'category_id', 'image']);

    $products = $products->map(function (Product $product) {
        return [
            'name'     => $product->name,
            'slug'     => $product->slug,
            'price'    => $product->price,
            'category' => optional($product->category)->name,
            'image'    => $product->image ? asset('storage/' . ltrim($product->image, '/')) : null,
        ];
    });

    return response()->json([
        'categories' => $categories,
        'products'   => $products,
    ]);
})->name('search.suggest');

/*
|--------------------------------------------------------------------------
| Winkelmand
|--------------------------------------------------------------------------
*/

Route::get('/winkelmand', function (CartPricing $pricing) {
    $fulfillmentMethod = session('fulfillment_method', 'delivery');
    $deliveryService = session('delivery_service', 'standard');
    $cart = $pricing->calculate(session('cart', []), $fulfillmentMethod);
    $deliveryCosts = $pricing->deliveryCosts($cart, $fulfillmentMethod, $deliveryService);

    return view('themes.default.pages.cart', compact('cart', 'fulfillmentMethod', 'deliveryService', 'deliveryCosts'));
})->name('cart.index');

Route::post('/winkelmand/levering', function (Request $request) {
    $data = $request->validate([
        'fulfillment_method' => ['required', Rule::in(['delivery', 'pickup'])],
    ]);

    session(['fulfillment_method' => $data['fulfillment_method']]);

    return back();
})->name('cart.fulfillment');

Route::post('/winkelmand/bezorgkeuze', function (Request $request) {
    $data = $request->validate([
        'delivery_option' => ['required', Rule::in(['standard', 'express', 'pickup'])],
    ]);

    session([
        'fulfillment_method' => $data['delivery_option'] === 'pickup' ? 'pickup' : 'delivery',
        'delivery_service' => $data['delivery_option'] === 'express' ? 'express' : 'standard',
    ]);

    return back();
})->name('cart.delivery-choice');

Route::post('/winkelmand/bezorgservice', function (Request $request) {
    $data = $request->validate([
        'delivery_service' => ['required', Rule::in(['standard', 'express'])],
    ]);

    session(['delivery_service' => $data['delivery_service']]);

    return back();
})->name('cart.delivery-service');

Route::post('/winkelmand/toevoegen/{id}', function ($id) {

    $product = Product::where('active', true)->findOrFail($id);
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            'name'     => $product->name,
            'price'    => $product->price,
            'quantity' => 1,
        ];
    }

    session()->put('cart', $cart);

    $total = collect(session('cart', []))->sum('quantity');

    if (request()->expectsJson()) {
        return response()->json([
            'message' => 'Product toegevoegd aan winkelmand',
            'count'   => $total,
        ]);
    }

    session()->flash('toast', 'Product toegevoegd aan winkelmand');

    return back();
})->name('cart.add');

Route::post('/winkelmand/actie/{promotion}', function (Promotion $promotion) {
    abort_unless($promotion->isCurrentlyActive() && $promotion->mainProduct?->active, 404);
    $cart = session()->get('cart', []);
    $productId = $promotion->main_product_id;
    $quantity = isset($cart[$productId]) && (int) ($cart[$productId]['promotion_id'] ?? 0) === $promotion->id
        ? max(1, (int) $cart[$productId]['quantity']) + 1
        : 1;
    $cart[$productId] = [
        'name' => $promotion->mainProduct->name,
        'price' => $promotion->fixed_price,
        'quantity' => $quantity,
        'promotion_id' => $promotion->id,
    ];
    session()->put('cart', $cart);
    return redirect()->route('cart.index')->with('toast', 'Actiebundel toegevoegd aan je winkelmand.');
})->name('cart.add-promotion');

Route::post('/winkelmand/bijwerken/{id}', function (Request $request, $id, CartPricing $pricing) {
    $validated = $request->validate([
        'quantity' => ['required', 'integer', 'min:1', 'max:999'],
    ]);
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = $validated['quantity'];
        session()->put('cart', $cart);
    }

    if ($request->expectsJson()) {
        $fulfillmentMethod = session('fulfillment_method', 'delivery');
        $deliveryService = session('delivery_service', 'standard');
        $pricedCart = $pricing->calculate($cart, $fulfillmentMethod);
        $item = $pricedCart[$id] ?? null;

        abort_unless($item, 404);

        return response()->json([
            'count' => collect($pricedCart)->sum('quantity'),
            'quantity' => $item['quantity'],
            'unit_price' => $item['price'],
            'base_price' => $item['base_price'],
            'item_subtotal' => round($item['price'] * $item['quantity'], 2),
            'total' => $pricing->total($pricedCart, $fulfillmentMethod, $deliveryService),
            'delivery_costs' => $pricing->deliveryCosts($pricedCart, $fulfillmentMethod, $deliveryService),
            'base_total' => round(collect($pricedCart)->sum(
                fn (array $cartItem) => $cartItem['base_price'] * $cartItem['quantity']
            ), 2),
            'tier_applied' => $item['tier_applied'],
            'discount_total' => $item['discount_total'],
            'cart_discount_total' => round(collect($pricedCart)->sum('discount_total'), 2),
            'tier_progress' => $item['tier_progress'],
        ]);
    }

    return back();
})->name('cart.update');

Route::post('/winkelmand/verwijderen/{id}', function ($id) {

    $cart = session()->get('cart', []);
    unset($cart[$id]);
    session()->put('cart', $cart);

    return back();
})->name('cart.remove');

// Legacy redirect
Route::permanentRedirect('/cart', '/winkelmand');

/*
|--------------------------------------------------------------------------
| Checkout (guest + ingelogd)
|--------------------------------------------------------------------------
*/

Route::get('/checkout', function (CartPricing $pricing) {
    $fulfillmentMethod = session('fulfillment_method', 'delivery');
    $deliveryService = session('delivery_service', 'standard');
    $cart = $pricing->calculate(session('cart', []), $fulfillmentMethod);
    $deliveryCosts = $pricing->deliveryCosts($cart, $fulfillmentMethod, $deliveryService);
    $provinces = nl_provinces();
    $paymentMethods = config('payments.methods', []);
    $pickupLocations = Location::query()->orderBy('name')->get();

    return view('themes.default.pages.checkout', compact('cart', 'provinces', 'paymentMethods', 'fulfillmentMethod', 'deliveryService', 'deliveryCosts', 'pickupLocations'));
})->name('checkout.index');

Route::post('/checkout', function (Request $request, CartPricing $pricing) {
    $fulfillmentMethod = session('fulfillment_method', 'delivery');
    $deliveryService = $fulfillmentMethod === 'delivery'
        ? session('delivery_service', 'standard')
        : 'standard';

    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email',
        'address'  => 'required|string|max:255',
        'postcode' => ['required', 'regex:/^[1-9][0-9]{3}\s?[A-Z]{2}$/i'],
        'city'     => 'required|string|max:255',
        'province' => ['required', 'in:' . implode(',', nl_provinces())],
        'payment_method' => ['required', Rule::in(array_keys(config('payments.methods', [])))],
        'pickup_location_id' => [
            $fulfillmentMethod === 'pickup' ? 'required' : 'nullable',
            'integer',
            Rule::exists('locations', 'id'),
        ],
    ], [
        'pickup_location_id.required' => 'Kies het depot waar je de bestelling wilt afhalen.',
        'pickup_location_id.exists' => 'Het gekozen depot bestaat niet meer. Kies een ander depot.',
    ]);

    $postcode = strtoupper(str_replace(' ', '', $request->postcode));
    $postcode = substr($postcode, 0, 4) . ' ' . substr($postcode, 4);

    $cart = $pricing->calculate(session('cart', []), $fulfillmentMethod);
    abort_if(empty($cart), 400);
    $deliveryCosts = $pricing->deliveryCosts($cart, $fulfillmentMethod, $deliveryService);

    $pickupLocation = $fulfillmentMethod === 'pickup'
        ? Location::findOrFail($request->integer('pickup_location_id'))
        : null;

    $isCashPayment = $request->payment_method === 'cash';

    $order = Order::createFromCart($cart, [
        'user_id'  => auth()->id(),
        'name'     => $request->name,
        'email'    => $request->email,
        'address'  => $request->address,
        'postcode' => $postcode,
        'city'     => $request->city,
        'province' => $request->province,
        'fulfillment_method' => $fulfillmentMethod,
        'delivery_service' => $deliveryService,
        'shipping_cost' => $deliveryCosts['total'],
        'pickup_location_id' => $pickupLocation?->id,
        'pickup_location_name' => $pickupLocation?->name,
        'pickup_location_address' => $pickupLocation
            ? trim($pickupLocation->street.', '.$pickupLocation->postcode_city, ' ,')
            : null,
        'pickup_location_opening' => $pickupLocation?->opening,
    ], $isCashPayment ? OrderStatus::PENDING : OrderStatus::AWAITING_PAYMENT, $deliveryCosts['total']);

    $payment = Payment::create([
        'order_id'           => $order->id,
        'provider'           => $isCashPayment ? 'manual' : config('payments.provider', 'mock'),
        'status'             => PaymentStatus::OPEN,
        'amount'             => $order->total,
        'currency'           => 'EUR',
        'due_date'           => $isCashPayment ? null : now()->addDays(14),
        'meta'                => [
            'payment_method' => $request->payment_method,
            'handling' => $isCashPayment ? 'cash_on_delivery' : 'online',
        ],
    ]);

    if (! $isCashPayment) {
        app(PaymentService::class)->ensurePayLink($payment);
    }

    if (auth()->check()) {
        auth()->user()->update([
            'address'  => $request->address,
            'postcode' => $postcode,
            'city'     => $request->city,
            'province' => $request->province,
        ]);
    }

    if ($isCashPayment) {
        session()->forget(['cart', 'fulfillment_method', 'delivery_service']);

        app()->terminating(function () use ($order): void {
            try {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            } catch (\Throwable $exception) {
                Log::error('Cash order confirmation email could not be sent.', [
                    'order_id' => $order->id,
                    'exception' => $exception,
                ]);
            }
        });

        $message = 'Bestelling geplaatst. Je betaalt € '.number_format($order->total, 2, ',', '.').' contant bij '.($fulfillmentMethod === 'pickup' ? 'het afhalen.' : 'de bezorging.');

        return auth()->check()
            ? redirect()->route('account.orders')->with('toast', $message)
            : redirect()->route('home')->with('toast', $message);
    }

    if ($payment->pay_link) {
        return redirect()->away($payment->pay_link, 303);
    }

    $order->delete();

    return redirect()->route('checkout.index')
        ->with('toast', 'De betaling kon niet worden gestart. Probeer het opnieuw.');
})->name('checkout.store');

/*
|--------------------------------------------------------------------------
| Account & profiel (ingelogd)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profiel', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profiel', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profiel', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Redirect legacy /account to profile edit (users edit their profile directly)
    Route::get('/account', fn () => redirect()->route('profile.edit'))
        ->name('account.dashboard');

    Route::get('/account/bestellingen', function () {
        $orders = auth()->user()
            ->orders()
            ->placed()
            ->withCount('items')
            ->latest()
            ->get();

        return view('account.orders', compact('orders'));
    })->name('account.orders');

    Route::get('/account/bestellingen/{order}', function (Order $order) {
        abort_unless($order->user_id === auth()->id() && ! $order->isAwaitingPayment(), 404);
        $order->load('items.product');

        return view('account.order-show', compact('order'));
    })->name('account.orders.show');

    // Re-order: restore available products to the cart and use the normal checkout flow.
    Route::post('/account/bestellingen/{order}/reorder', function (Order $order) {
        abort_unless($order->user_id === auth()->id() && ! $order->isAwaitingPayment(), 404);

        $order->load('items');
        $productIds = $order->items->pluck('product_id')->filter()->unique();
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('active', true)
            ->get()
            ->keyBy('id');
        $cart = session('cart', []);
        $added = 0;

        foreach ($order->items as $item) {
            $product = $products->get($item->product_id);

            if (! $product) {
                continue;
            }

            $currentQuantity = (int) ($cart[$product->id]['quantity'] ?? 0);
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $currentQuantity + $item->quantity,
            ];
            $added++;
        }

        if ($added === 0) {
            return back()->with('toast', 'De producten uit deze bestelling zijn niet meer beschikbaar.');
        }

        session([
            'cart' => $cart,
            'fulfillment_method' => in_array($order->fulfillment_method, ['delivery', 'pickup'], true)
                ? $order->fulfillment_method
                : 'delivery',
            'delivery_service' => in_array($order->delivery_service, ['standard', 'express'], true)
                ? $order->delivery_service
                : 'standard',
        ]);

        $unavailable = $order->items->count() - $added;
        $message = $unavailable > 0
            ? 'Beschikbare producten zijn toegevoegd. '.$unavailable.' product(en) zijn niet meer leverbaar.'
            : 'Alle producten zijn toegevoegd aan je winkelmand. Controleer de actuele prijzen en rond je bestelling af.';

        return redirect()->route('cart.index')->with('toast', $message);
    })->name('account.orders.reorder');

    // Download invoice PDF
    Route::get('/account/bestellingen/{order}/invoice', function (Order $order) {
        abort_unless($order->user_id === auth()->id() && ! $order->isAwaitingPayment(), 404);

        $pdf = Pdf::loadView('pdfs.invoice', compact('order'))->setPaper('a4');

        return $pdf->download('factuur-' . $order->id . '.pdf');
    })->name('account.orders.invoice');

    Route::get('/dashboard', fn () => redirect()->route('profile.edit'))->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Redirect /admin → /admin/dashboard
        Route::get('/', fn () => redirect()->route('admin.dashboard'));

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::view('/help', 'admin.help')->name('help');

        Route::get('/audit', [AuditLogController::class, 'index'])
            ->name('audit.index');

        // CMS content
        Route::get('/content', [ContentController::class, 'edit'])
            ->name('content.edit');
        Route::post('/content', [ContentController::class, 'update'])
            ->name('content.update');

        Route::get('/verzendregels', fn () => redirect()->route('admin.products.index'))
            ->name('shipping-rules.edit');
        Route::put('/verzendregels', fn () => redirect()->route('admin.products.index'))
            ->name('shipping-rules.update');

        // Locations
        Route::get('/locaties', [LocationController::class, 'index'])
            ->name('locations.index');
        Route::get('/locaties/nieuw', [LocationController::class, 'create'])
            ->name('locations.create');
        Route::post('/locaties', [LocationController::class, 'store'])
            ->name('locations.store');
        Route::get('/locaties/{location}/edit', [LocationController::class, 'edit'])
            ->name('locations.edit');
        Route::put('/locaties/{location}', [LocationController::class, 'update'])
            ->name('locations.update');
        Route::patch('/locaties/{location}/zichtbaarheid', [LocationController::class, 'toggleVisibility'])
            ->name('locations.toggle-visibility');
        Route::delete('/locaties/{location}', [LocationController::class, 'destroy'])
            ->name('locations.destroy');

        // Maintenance mode toggle (site-wide except admins)
        Route::post('/maintenance/toggle', function () {
            $enabled = Setting::getBool('maintenance_enabled', false);
            Setting::set('maintenance_enabled', $enabled ? '0' : '1');

            return back()->with('toast', $enabled ? 'Onderhoudsmodus uitgezet' : 'Onderhoudsmodus aangezet');
        })->name('maintenance.toggle');

        // Orders
        Route::get('/orders/create', [ManualOrderController::class, 'create'])
            ->name('orders.create');
        Route::post('/orders', [ManualOrderController::class, 'store'])
            ->name('orders.store');

        Route::resource('promotions', PromotionController::class)->except('show');

        Route::get('/orders', function (Request $request) {
            $provinces = nl_provinces();

            $filters = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'tab' => ['nullable', Rule::in(['all', 'new', 'paid', 'unpaid', 'planned', 'shipped', 'completed', 'cancelled'])],
                'province' => ['nullable', Rule::in($provinces)],
                'order_date' => ['nullable', 'date'],
                'payment_status' => ['nullable', Rule::in(array_column(PaymentStatus::cases(), 'value'))],
                'fulfillment_method' => ['nullable', Rule::in(['delivery', 'pickup'])],
            ]);

            $orders = Order::query()
                ->placed()
                ->with('latestPayment')
                ->withSum('items as item_quantity', 'quantity')
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('id', ctype_digit($search) ? (int) $search : 0)
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('postcode', 'like', "%{$search}%");
                    });
                })
                ->when($filters['province'] ?? null, fn ($q, $province) => $q->where('province', $province))
                ->when($filters['order_date'] ?? null, fn ($q, $orderDate) => $q->whereDate('created_at', $orderDate))
                ->when($filters['payment_status'] ?? null, fn ($q, $status) => $q->whereHas(
                    'latestPayment',
                    fn ($payment) => $payment->where('status', $status)
                ))
                ->when($filters['fulfillment_method'] ?? null, fn ($q, $method) => $q->where('fulfillment_method', $method))
                ->when(($filters['tab'] ?? 'all') === 'new', fn ($q) => $q->where('status', OrderStatus::PENDING))
                ->when(($filters['tab'] ?? 'all') === 'paid', fn ($q) => $q->whereHas(
                    'latestPayment',
                    fn ($payment) => $payment->where('status', PaymentStatus::PAID)
                ))
                ->when(($filters['tab'] ?? 'all') === 'unpaid', fn ($q) => $q->whereHas(
                    'latestPayment',
                    fn ($payment) => $payment->where('status', PaymentStatus::OPEN)
                ))
                ->when(($filters['tab'] ?? 'all') === 'planned', fn ($q) => $q->whereNotNull('delivery_route_id'))
                ->when(($filters['tab'] ?? 'all') === 'shipped', fn ($q) => $q->where('status', OrderStatus::SHIPPED))
                ->when(($filters['tab'] ?? 'all') === 'completed', fn ($q) => $q->where('status', OrderStatus::COMPLETED))
                ->when(($filters['tab'] ?? 'all') === 'cancelled', fn ($q) => $q->where('status', OrderStatus::CANCELLED))
                ->orderByDesc('created_at')
                ->paginate(25)
                ->withQueryString();

            $stats = [
                'new' => Order::query()->placed()->where('status', OrderStatus::PENDING)->count(),
                'awaiting_payment' => Order::query()->placed()->whereHas(
                    'latestPayment',
                    fn ($payment) => $payment->where('status', PaymentStatus::OPEN)
                )->count(),
                'ready_to_plan' => Order::query()
                    ->placed()
                    ->where('fulfillment_method', 'delivery')
                    ->whereNull('delivery_route_id')
                    ->where('status', OrderStatus::PENDING)
                    ->count(),
                'shipped_today' => Order::query()
                    ->placed()
                    ->where('status', OrderStatus::SHIPPED)
                    ->whereDate('updated_at', today())
                    ->count(),
            ];

            return view('admin.orders.index', compact('orders', 'provinces', 'filters', 'stats'));
        })->name('orders.index');

        Route::get('/orders/{order}', function (Order $order) {
            abort_if($order->isAwaitingPayment(), 404);
            $order->load(['items.product', 'latestPayment.events.actor']);

            $provinces = nl_provinces();
            $routeDate = $order->route_date?->toDateString() ?? now()->toDateString();

            $deliveryRoutes = DeliveryRoute::query()
                ->whereDate('route_date', $routeDate)
                ->orderBy('name')
                ->get();

            return view('admin.orders.show', compact('order', 'provinces', 'deliveryRoutes'));
        })->name('orders.show');

        Route::patch('/orders/{order}/plan', function (Request $request, Order $order) {
            abort_if($order->isAwaitingPayment(), 404);

            $provinces = nl_provinces();

            $data = $request->validate([
                'province'       => ['nullable', 'in:' . implode(',', $provinces)],
                'route_date'     => ['nullable', 'date'],
                'route_sequence' => ['nullable', 'integer', 'min:1', 'max:65535'],
                'delivery_route_id' => ['nullable', 'exists:delivery_routes,id'],
            ]);

            if (! empty($data['delivery_route_id'])) {
                $route = DeliveryRoute::find($data['delivery_route_id']);
                if ($route) {
                    $data['route_date'] = $route->route_date->toDateString();
                    if (! empty($route->province)) {
                        $data['province'] = $route->province;
                    }
                }
            } else {
                $data['delivery_route_id'] = null;
            }

            $order->update($data);

            return back()->with('toast', 'Route planning opgeslagen');
        })->name('orders.plan');

        Route::post('/orders/{order}/ship', function (Order $order) {
            abort_if($order->isAwaitingPayment(), 404);

            $order->update(['status' => OrderStatus::SHIPPED]);

            if ($order->email) {
                Mail::to($order->email)->send(new OrderShippedMail($order));
            }

            return back()->with('toast', $order->email ? 'Verzendmail verstuurd' : 'Bestelling gemarkeerd als verzonden');
        })->name('orders.ship');

        Route::post('/orders/{order}/complete', function (Order $order) {
            abort_if($order->isAwaitingPayment(), 404);
            abort_unless(in_array($order->status, [OrderStatus::PENDING, OrderStatus::SHIPPED], true), 422);

            $order->update(['status' => OrderStatus::COMPLETED]);

            return back()->with('toast', 'Bestelling #'.$order->id.' is afgerond.');
        })->name('orders.complete');

        Route::patch('/orders/{order}/notes', function (Request $request, Order $order) {
            abort_if($order->isAwaitingPayment(), 404);

            $data = $request->validate([
                'route_notes' => ['nullable', 'string', 'max:2000'],
            ]);

            $order->update([
                'route_notes' => filled($data['route_notes'] ?? null)
                    ? trim($data['route_notes'])
                    : null,
            ]);

            return back()->with('toast', 'Opmerking bij bestelling #'.$order->id.' opgeslagen.');
        })->name('orders.notes');

        // Routes (planning overzicht)
        Route::get('/routes/slim-plannen', [SmartRouteController::class, 'index'])
            ->name('routes.smart');
        Route::post('/routes/slim-plannen/voorbeeld', [SmartRouteController::class, 'preview'])
            ->name('routes.smart.preview');
        Route::post('/routes/slim-plannen/bevestigen', [SmartRouteController::class, 'store'])
            ->name('routes.smart.store');
        Route::post('/routes/slim-plannen/instellingen', [SmartRouteController::class, 'updateSettings'])
            ->name('routes.smart.settings');

        Route::get('/routes', [SmartRouteController::class, 'manage'])
            ->name('routes.index');
        Route::get('/routes/{deliveryRoute}/laden', [SmartRouteController::class, 'loading'])
            ->name('routes.loading');
        Route::patch('/routes/{deliveryRoute}/laden', [SmartRouteController::class, 'toggleLoadingItem'])
            ->name('routes.loading.toggle');
        Route::delete('/routes/{deliveryRoute}', [SmartRouteController::class, 'destroy'])
            ->name('routes.destroy');

        Route::post('/routes', function (Request $request) {
            $provinces = nl_provinces();

            $data = $request->validate([
                'route_date'   => ['required', 'date'],
                'name'         => ['required', 'string', 'max:255', Rule::unique('delivery_routes', 'name')],
                'province'     => ['nullable', 'in:' . implode(',', $provinces)],
                'admin_user_id' => [
                    'nullable',
                    Rule::exists('users', 'id')->where('is_admin', true),
                ],
            ], [
                'name.unique' => 'Deze routenaam bestaat al. Kies een andere routenaam.',
            ]);

            $route = DeliveryRoute::create([
                'route_date' => $data['route_date'],
                'name' => $data['name'],
                'province' => $data['province'] ?? null,
                'admin_id' => $data['admin_user_id'] ?? null,
            ]);

            return redirect()->route('admin.routes.index', [
                'route_date' => $route->route_date->toDateString(),
                'province' => $route->province,
                'route_id' => $route->id,
            ])->with('toast', 'Route aangemaakt.');
        })->name('routes.store');

        Route::post('/routes/bulk-create', function (Request $request) {
            $provinces = nl_provinces();

            $data = $request->validate([
                'route_date'         => ['required', 'date'],
                'admin_user_id'      => ['nullable', Rule::exists('users', 'id')->where('is_admin', true)],
                'route_date_filter'  => ['nullable', 'date'],
                'order_date_filter'  => ['nullable', 'date'],
                'province_filter'    => ['nullable', 'in:' . implode(',', $provinces)],
                'only_planned_filter' => ['nullable', 'boolean'],
            ]);

            $ordersQuery = Order::query()
                ->placed()
                ->when($data['province_filter'] ?? null, fn ($q, $province) => $q->where('province', $province))
                ->when($data['route_date_filter'] ?? null, fn ($q, $routeDate) => $q->whereDate('route_date', $routeDate))
                ->when($data['order_date_filter'] ?? null, fn ($q, $orderDate) => $q->whereDate('created_at', $orderDate))
                ->when(!empty($data['only_planned_filter']), fn ($q) => $q->whereNotNull('route_date'));

            $orders = $ordersQuery->get();

            if ($orders->isEmpty()) {
                return back()->with('toast', 'Geen bestellingen gevonden voor bulk route.');
            }

            $route = DeliveryRoute::create([
                'route_date' => $data['route_date'],
                'name' => 'Bulk ' . \Carbon\Carbon::parse($data['route_date'])->format('d-m-Y'),
                'province' => $data['province_filter'] ?? null,
                'admin_id' => $data['admin_user_id'] ?? null,
            ]);

            $mapboxToken = config('services.mapbox.token');
            $coords = [];
            $coordOrderIds = [];
            $addressHashById = [];

            foreach ($orders as $order) {
                $addressKey = trim($order->address . '|' . $order->postcode . '|' . $order->city . '|' . ($order->province ?? ''));
                $addressHash = hash('sha256', strtolower($addressKey));
                $addressHashById[$order->id] = $addressHash;

                if ($order->geo_lat && $order->geo_lng && $order->geo_address_hash === $addressHash) {
                    $coords[] = [$order->geo_lng, $order->geo_lat];
                    $coordOrderIds[] = $order->id;
                    continue;
                }

                $query = trim($order->address . ', ' . $order->postcode . ' ' . $order->city . ', ' . ($order->province ?? 'Nederland'));
                $geoUrl = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($query) . '.json';
                $geoResponse = Http::get($geoUrl, [
                    'access_token' => $mapboxToken,
                    'limit' => 1,
                    'country' => 'nl',
                    'language' => 'nl',
                ]);

                if ($geoResponse->ok() && !empty($geoResponse->json('features.0.center'))) {
                    $center = $geoResponse->json('features.0.center');
                    $lon = $center[0] ?? null;
                    $lat = $center[1] ?? null;

                    if ($lon !== null && $lat !== null) {
                        $order->update([
                            'geo_lat' => $lat,
                            'geo_lng' => $lon,
                            'geo_address_hash' => $addressHash,
                        ]);

                        $coords[] = [$lon, $lat];
                        $coordOrderIds[] = $order->id;
                        continue;
                    }
                }
            }

            $orderedIds = [];
            $maxOptimizable = 25;

            if ($mapboxToken && count($coords) >= 2 && count($coords) <= $maxOptimizable) {
                $coordPairs = collect($coords)
                    ->map(fn ($c) => $c[0] . ',' . $c[1])
                    ->implode(';');

                $optUrl = 'https://api.mapbox.com/optimized-trips/v1/mapbox/driving/' . $coordPairs;
                $optResponse = Http::get($optUrl, [
                    'access_token' => $mapboxToken,
                    'roundtrip' => 'false',
                    'source' => 'first',
                    'destination' => 'last',
                ]);

                if ($optResponse->ok() && !empty($optResponse->json('waypoints'))) {
                    $waypoints = $optResponse->json('waypoints');
                    $indexed = collect($waypoints)
                        ->sortBy('waypoint_index')
                        ->pluck('waypoint_index', 'location_index');

                    $orderedIds = collect($coordOrderIds)
                        ->mapWithKeys(fn ($id, $idx) => [$idx => $id])
                        ->sortBy(fn ($id, $idx) => $indexed[$idx] ?? $idx)
                        ->values()
                        ->all();
                }
            }

            if (empty($orderedIds)) {
                $orderedIds = $orders->sortBy([
                    fn ($o) => $o->postcode ?? '',
                    fn ($o) => $o->address ?? '',
                    fn ($o) => $o->city ?? '',
                ])->pluck('id')->values()->all();
            }

            foreach ($orderedIds as $index => $orderId) {
                $order = Order::find($orderId);
                if (! $order) {
                    continue;
                }
                Order::where('id', $orderId)->update([
                    'delivery_route_id' => $route->id,
                    'assigned_admin_id' => $route->admin_id,
                    'route_date' => $route->route_date,
                    'province' => $route->province ?? $order->province,
                    'route_sequence' => $index + 1,
                ]);
            }

            return redirect()->route('admin.routes.index', [
                'route_date' => $route->route_date->toDateString(),
                'province' => $route->province,
                'route_id' => $route->id,
            ])->with('toast', 'Bulk route aangemaakt en gesorteerd.');
        })->name('routes.bulk-create');

        Route::post('/routes/assign-admin', function (Request $request) {
            $data = $request->validate([
                'route_id' => ['required', 'exists:delivery_routes,id'],
                'admin_user_id' => [
                    'nullable',
                    Rule::exists('users', 'id')->where('is_admin', true),
                ],
            ]);

            $route = DeliveryRoute::findOrFail($data['route_id']);
            $adminId = $data['admin_user_id'] ?? null;

            $route->update(['admin_id' => $adminId]);

            Order::where('delivery_route_id', $route->id)
                ->update(['assigned_admin_id' => $adminId]);

            $message = $adminId
                ? 'Route gekoppeld aan chauffeur.'
                : 'Route koppeling verwijderd.';

            return back()->with('toast', $message);
        })->name('routes.assign-admin');

        Route::post('/routes/{deliveryRoute}/ship', function (DeliveryRoute $deliveryRoute) {
            $orders = $deliveryRoute->orders()
                ->placed()
                ->with('items')
                ->get();

            if ($orders->isEmpty()) {
                return back()->with('toast', 'Deze route bevat geen bestellingen.');
            }

            $mailed = 0;

            foreach ($orders as $order) {
                $order->update(['status' => OrderStatus::SHIPPED]);

                if ($order->email) {
                    Mail::to($order->email)->send(new OrderShippedMail($order));
                    $mailed++;
                }
            }

            $withoutEmail = $orders->count() - $mailed;
            $message = "Verzendmail verstuurd naar {$mailed} bestelling(en).";

            if ($withoutEmail > 0) {
                $message .= " {$withoutEmail} bestelling(en) hadden geen e-mailadres, maar zijn wel als verzonden gemarkeerd.";
            }

            return back()->with('toast', $message);
        })->name('routes.ship');

        Route::post('/routes/resequence', function (Request $request) {
            $data = $request->validate([
                'route_id'         => ['required', 'exists:delivery_routes,id'],
                'order_ids'        => ['nullable', 'array'],
                'order_ids.*'      => ['integer', 'exists:orders,id'],
            ]);

            $ids = $data['order_ids'] ?? [];

            foreach ($ids as $index => $orderId) {
                Order::where('id', $orderId)
                    ->where('delivery_route_id', $data['route_id'])
                    ->update([
                        'route_sequence' => $index + 1,
                    ]);
            }

            return back()->with('toast', 'Volgorde bijgewerkt');
        })->name('routes.resequence');

        Route::patch('/routes/{order}/timing', function (Request $request, Order $order) {
            $data = $request->validate([
                'route_sequence'       => ['nullable', 'integer', 'min:1', 'max:65535'],
                'route_travel_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
                'route_stop_minutes'   => ['nullable', 'integer', 'min:0', 'max:1440'],
                'route_notes'          => ['nullable', 'string', 'max:2000'],
            ]);

            $order->update($data);

            return back()->with('toast', 'Routegegevens opgeslagen');
        })->name('routes.timing');

        Route::patch('/routes/{order}/remove', function (Order $order) {
            $order->update([
                'delivery_route_id'    => null,
                'route_date'           => null,
                'province'             => null,
                'route_sequence'       => null,
                'route_travel_minutes' => null,
                'route_stop_minutes'   => null,
            ]);

            return back()->with('toast', 'Stop verwijderd uit route');
        })->name('routes.remove');

        // Product active toggle
        Route::patch(
            '/products/{product}/toggle-active',
            [ProductController::class, 'toggleActive']
        )->name('products.toggle-active');

        // Product featured toggle (for homepage slider)
        Route::patch(
            '/products/{product}/toggle-featured',
            [ProductController::class, 'toggleFeatured']
        )->name('products.toggle-featured');

        // Product CRUD
        Route::resource('products', ProductController::class)
            ->except(['show']);

        // Category CRUD
        Route::resource('categories', CategoryController::class)
            ->except(['show']);

        // Users CRUD (admin)
        Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])
            ->name('users.toggle-admin');

        Route::resource('users', UserController::class)
            ->only(['index', 'edit', 'update', 'destroy']);

        // Payments (achteraf betalen)
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/send-request', [PaymentController::class, 'sendPaymentRequest'])->name('payments.send-request');
        Route::patch('/payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.mark-paid');

        // Newsletters
        Route::get('/newsletters-doelgroep/aantal', [NewsletterController::class, 'audienceCount'])->name('newsletters.audience-count');
        Route::resource('newsletters', NewsletterController::class)->except(['destroy']);
        Route::post('/newsletters/{newsletter}/send', [NewsletterController::class, 'send'])->name('newsletters.send');
        Route::post('/newsletters/{newsletter}/schedule', [NewsletterController::class, 'schedule'])->name('newsletters.schedule');
        Route::post('/newsletters/{newsletter}/cancel', [NewsletterController::class, 'cancel'])->name('newsletters.cancel');
        Route::post('/newsletters/{newsletter}/duplicate', [NewsletterController::class, 'duplicate'])->name('newsletters.duplicate');
        Route::post('/newsletters/{newsletter}/test', [NewsletterController::class, 'test'])->name('newsletters.test');
    });

// Payment webhooks
Route::post('/webhooks/payments/{provider}', [PaymentWebhookController::class, 'handle'])->name('payments.webhook');
Route::get('/betaling/terug/{payment}', [PaymentWebhookController::class, 'returnFromProvider'])
    ->middleware('signed')
    ->name('payments.return');

// Nieuwsbrief uitschrijven
Route::get('/newsletter/unsubscribe', NewsletterUnsubscribeController::class)
    ->middleware('signed')
    ->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| Auth (login / register / logout)
|--------------------------------------------------------------------------
*/

// Postcode lookup proxy
Route::get('/api/postcode-lookup', \App\Http\Controllers\PostcodeLookupController::class)
    ->name('api.postcode-lookup');

require __DIR__.'/auth.php';

// Sitemap
Route::get('/sitemap.xml', function () {
    abort_if(str_starts_with(request()->getHost(), 'dev.') || app()->environment(['local', 'development', 'testing']), 404);

    $products = Product::where('active', true)->get();
    $categories = Category::all();

    $urls = [];

    $urls[] = ['loc' => url('/'), 'priority' => '1.0'];
    $urls[] = ['loc' => route('products.liquids'), 'priority' => '0.9'];
    $urls[] = ['loc' => route('products.heaters'), 'priority' => '0.8'];
    $urls[] = ['loc' => route('products.index'), 'priority' => '0.7'];
    $urls[] = ['loc' => route('informatie'), 'priority' => '0.6'];
    $urls[] = ['loc' => route('locaties'), 'priority' => '0.6'];
    $urls[] = ['loc' => route('over-ons'), 'priority' => '0.5'];

    foreach ($categories as $cat) {
        $urls[] = ['loc' => route('category.show', $cat->slug), 'priority' => '0.7'];
    }

    foreach ($products as $p) {
        $urls[] = ['loc' => route('product.show', $p->slug), 'lastmod' => $p->updated_at->toAtomString(), 'priority' => '0.8'];
    }

    $xml = view('sitemap', compact('urls'))->render();

    return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
});
