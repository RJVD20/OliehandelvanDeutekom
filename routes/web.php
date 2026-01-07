<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\NewsletterUnsubscribeController;

/*
|--------------------------------------------------------------------------
| Mail
|--------------------------------------------------------------------------
*/
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderShippedMail;

if (! function_exists('nl_provinces')) {
    function nl_provinces(): array
    {
        return [
            'Drenthe',
            'Flevoland',
            'Friesland',
            'Gelderland',
            'Groningen',
            'Limburg',
            'Noord-Brabant',
            'Noord-Holland',
            'Overijssel',
            'Utrecht',
            'Zeeland',
            'Zuid-Holland',
        ];
    }
}

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
use App\Enums\PaymentStatus;
use App\Services\Payments\PaymentService;
use Illuminate\Support\Facades\Response;
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

    return view('themes.default.pages.home', compact('products', 'categories'));
})->name('home');

Route::get('/informatie', function () {
    return view('themes.default.pages.informatie');
})->name('informatie');

Route::get('/over-ons', function () {
    return view('themes.default.pages.over-ons');
})->name('over-ons');

Route::get('/locaties', function () {
    $locaties = Location::orderBy('name')->get();
    return view('themes.default.pages.locaties', compact('locaties'));
})->name('locaties');

/*
|--------------------------------------------------------------------------
| Producten (frontend)
|--------------------------------------------------------------------------
*/

Route::get('/product/{slug}', function ($slug) {
    $product = Product::with('category')->where('slug', $slug)->firstOrFail();

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

    return view('themes.default.pages.product', compact('product', 'suggestedProducts'));
})->name('product.show');

Route::get('/categories/{slug}', function ($slug) {
    $category = Category::where('slug', $slug)->firstOrFail();
    $products = $category->products()->where('active', true)->paginate(12);

    return view('themes.default.pages.category', compact('category', 'products'));
})->name('category.show');



Route::get('/producten', function (Request $request) {

    $query = Product::where('active', true);

    if ($request->filled('categories')) {
        $query->whereIn('category_id', $request->categories);
    }

    if ($request->filled('types')) {
        $query->whereIn('type', $request->types);
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
    $categories = Category::all();

    return view('themes.default.pages.products.index', compact('products', 'categories'));
})->name('products.index');

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

Route::get('/winkelmand', function () {
    $cart = session('cart', []);
    return view('themes.default.pages.cart', compact('cart'));
})->name('cart.index');

Route::post('/winkelmand/toevoegen/{id}', function ($id) {

    $product = Product::findOrFail($id);
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
    session()->flash('toast', 'Product toegevoegd aan winkelmand');

    return back();
})->name('cart.add');

Route::post('/winkelmand/bijwerken/{id}', function (Request $request, $id) {

    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = max(1, (int) $request->quantity);
        session()->put('cart', $cart);
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

Route::get('/checkout', function () {
    $cart = session('cart', []);
    $provinces = nl_provinces();

    return view('themes.default.pages.checkout', compact('cart', 'provinces'));
})->name('checkout.index');

Route::post('/checkout', function (Request $request) {

    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email',
        'address'  => 'required|string|max:255',
        'postcode' => ['required', 'regex:/^[1-9][0-9]{3}\s?[A-Z]{2}$/i'],
        'city'     => 'required|string|max:255',
        'province' => ['required', 'in:' . implode(',', nl_provinces())],
    ]);

    $postcode = strtoupper(str_replace(' ', '', $request->postcode));
    $postcode = substr($postcode, 0, 4) . ' ' . substr($postcode, 4);

    $cart = session('cart', []);
    abort_if(empty($cart), 400);

    $total = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

    $order = Order::create([
        'user_id' => auth()->id(),
        'status'  => 'pending',
        'total'   => $total,
        'name'    => $request->name,
        'email'   => $request->email,
        'address' => $request->address,
        'postcode'=> $postcode,
        'city'    => $request->city,
        'province'=> $request->province,
    ]);

    foreach ($cart as $productId => $item) {
        $order->items()->create([
            'product_id'   => $productId,
            'product_name' => $item['name'],
            'price'        => $item['price'],
            'quantity'     => $item['quantity'],
        ]);
    }

    $payment = Payment::create([
        'order_id'           => $order->id,
        'provider'           => config('payments.provider', 'mock'),
        'status'             => PaymentStatus::OPEN,
        'amount'             => $total,
        'currency'           => 'EUR',
        'due_date'           => now()->addDays(14),
        'reminder_count'     => 0,
        'last_reminder_at'   => null,
    ]);

    app(PaymentService::class)->ensurePayLink($payment);

    if (auth()->check()) {
        auth()->user()->update([
            'address'  => $request->address,
            'postcode' => $postcode,
            'city'     => $request->city,
            'province' => $request->province,
        ]);
    }

    Mail::to($order->email)->send(new OrderConfirmationMail($order));
    session()->forget('cart');

    return auth()->check()
        ? redirect()->route('account.orders')->with('toast', 'Bestelling geplaatst 🎉')
        : redirect()->route('home')->with('toast', 'Bestelling geplaatst 🎉 Check je e-mail.');
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

    Route::get('/account/bestellingen', fn () => view('account.orders'))->name('account.orders');

    Route::get('/account/bestellingen/{order}', function (Order $order) {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('account.order-show', compact('order'));
    })->name('account.orders.show');

    // Re-order: place the same order again
    Route::post('/account/bestellingen/{order}/reorder', function (Order $order) {
        abort_unless($order->user_id === auth()->id(), 403);

        $new = Order::create([
            'user_id' => auth()->id(),
            'status'  => 'pending',
            'total'   => $order->total,
            'name'    => $order->name,
            'email'   => $order->email,
            'address' => $order->address,
            'postcode'=> $order->postcode,
            'city'    => $order->city,
            'province'=> $order->province,
        ]);

        foreach ($order->items as $item) {
            $new->items()->create([
                'product_id'   => $item->product_id,
                'product_name' => $item->product_name,
                'price'        => $item->price,
                'quantity'     => $item->quantity,
            ]);
        }

        Mail::to($new->email)->send(new OrderConfirmationMail($new));

        return redirect()->route('account.orders')->with('toast', 'Bestelling opnieuw geplaatst');
    })->name('account.orders.reorder');

    // Download invoice PDF
    Route::get('/account/bestellingen/{order}/invoice', function (Order $order) {
        abort_unless($order->user_id === auth()->id(), 403);

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

        // Maintenance mode toggle (site-wide except admins)
        Route::post('/maintenance/toggle', function () {
            $enabled = Setting::getBool('maintenance_enabled', false);
            Setting::set('maintenance_enabled', $enabled ? '0' : '1');

            return back()->with('toast', $enabled ? 'Onderhoudsmodus uitgezet' : 'Onderhoudsmodus aangezet');
        })->name('maintenance.toggle');

        // Orders
        Route::get('/orders', function (Request $request) {
            $provinces = nl_provinces();

            $filters = $request->validate([
                'province'     => ['nullable', 'in:' . implode(',', $provinces)],
                'route_date'   => ['nullable', 'date'],
                'only_planned' => ['nullable', 'boolean'],
            ]);

            $orders = Order::query()
                ->when($filters['province'] ?? null, fn ($q, $province) => $q->where('province', $province))
                ->when($filters['route_date'] ?? null, fn ($q, $routeDate) => $q->whereDate('route_date', $routeDate))
                ->when($request->boolean('only_planned'), fn ($q) => $q->whereNotNull('route_date'))
                ->orderByRaw('route_date IS NULL')
                ->orderBy('route_date')
                ->orderByRaw('route_sequence IS NULL')
                ->orderBy('route_sequence')
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();

            return view('admin.orders.index', compact('orders', 'provinces', 'filters'));
        })->name('orders.index');

        Route::get('/orders/{order}', function (Order $order) {
            $provinces = nl_provinces();
            return view('admin.orders.show', compact('order', 'provinces'));
        })->name('orders.show');

        Route::patch('/orders/{order}/plan', function (Request $request, Order $order) {
            $provinces = nl_provinces();

            $data = $request->validate([
                'province'       => ['nullable', 'in:' . implode(',', $provinces)],
                'route_date'     => ['nullable', 'date'],
                'route_sequence' => ['nullable', 'integer', 'min:1', 'max:65535'],
            ]);

            $order->update($data);

            return back()->with('toast', 'Route planning opgeslagen');
        })->name('orders.plan');

        Route::post('/orders/{order}/ship', function (Order $order) {
            $order->update(['status' => 'shipped']);

            Mail::to($order->email)->send(new OrderShippedMail($order));

            return back()->with('toast', 'Verzendmail verstuurd');
        })->name('orders.ship');

        // Routes (planning overzicht)
        Route::get('/routes', function (Request $request) {
            $provinces = nl_provinces();

            $filters = $request->validate([
                'route_date' => ['nullable', 'date'],
                'province'   => ['nullable', 'in:' . implode(',', $provinces)],
            ]);

            $routeDate = $filters['route_date'] ?? now()->toDateString();

            $orders = Order::query()
                ->whereDate('route_date', $routeDate)
                ->when($filters['province'] ?? null, fn ($q, $province) => $q->where('province', $province))
                ->orderByRaw('route_sequence IS NULL')
                ->orderBy('route_sequence')
                ->orderBy('id')
                ->get();

            $mapboxToken = config('services.mapbox.token');

            return view('admin.routes.index', compact('orders', 'routeDate', 'provinces', 'filters', 'mapboxToken'));
        })->name('routes.index');

        Route::post('/routes/resequence', function (Request $request) {
            $provinces = nl_provinces();

            $data = $request->validate([
                'route_date'       => ['required', 'date'],
                'province'         => ['nullable', 'in:' . implode(',', $provinces)],
                'order_ids'        => ['nullable', 'array'],
                'order_ids.*'      => ['integer', 'exists:orders,id'],
            ]);

            $ids = $data['order_ids'] ?? [];

            foreach ($ids as $index => $orderId) {
                Order::where('id', $orderId)->update([
                    'route_date'     => $data['route_date'],
                    'province'       => $data['province'] ?? null,
                    'route_sequence' => $index + 1,
                ]);
            }

            return back()->with('toast', 'Volgorde bijgewerkt');
        })->name('routes.resequence');

        Route::patch('/routes/{order}/timing', function (Request $request, Order $order) {
            $provinces = nl_provinces();

            $data = $request->validate([
                'province'             => ['nullable', 'in:' . implode(',', $provinces)],
                'route_date'           => ['nullable', 'date'],
                'route_sequence'       => ['nullable', 'integer', 'min:1', 'max:65535'],
                'route_travel_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
                'route_stop_minutes'   => ['nullable', 'integer', 'min:0', 'max:1440'],
                'route_notes'          => ['nullable', 'string'],
            ]);

            $order->update($data);

            return back()->with('toast', 'Routegegevens opgeslagen');
        })->name('routes.timing');

        Route::patch('/routes/{order}/remove', function (Order $order) {
            $order->update([
                'route_date'           => null,
                'route_sequence'       => null,
                'route_travel_minutes' => null,
                'route_stop_minutes'   => null,
                'route_notes'          => null,
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

        // Users CRUD (admin)
        Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])
            ->name('users.toggle-admin');

        Route::resource('users', UserController::class)
            ->except(['show']);

        // Payments (achteraf betalen)
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/remind', [PaymentController::class, 'remind'])->name('payments.remind');
        Route::patch('/payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.mark-paid');

        // Newsletters
        Route::resource('newsletters', NewsletterController::class)->except(['destroy']);
        Route::post('/newsletters/{newsletter}/send', [NewsletterController::class, 'send'])->name('newsletters.send');
        Route::post('/newsletters/{newsletter}/schedule', [NewsletterController::class, 'schedule'])->name('newsletters.schedule');
        Route::post('/newsletters/{newsletter}/cancel', [NewsletterController::class, 'cancel'])->name('newsletters.cancel');
        Route::post('/newsletters/{newsletter}/duplicate', [NewsletterController::class, 'duplicate'])->name('newsletters.duplicate');
        Route::post('/newsletters/{newsletter}/test', [NewsletterController::class, 'test'])->name('newsletters.test');
    });

// Payment webhooks
Route::post('/webhooks/payments/{provider}', [PaymentWebhookController::class, 'handle'])->name('payments.webhook');

// Nieuwsbrief uitschrijven
Route::get('/newsletter/unsubscribe', NewsletterUnsubscribeController::class)
    ->middleware('signed')
    ->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| Auth (login / register / logout)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

// Sitemap
Route::get('/sitemap.xml', function () {
    $products = Product::where('active', true)->get();
    $categories = Category::all();

    $urls = [];

    $urls[] = ['loc' => url('/'), 'priority' => '1.0'];
    $urls[] = ['loc' => route('informatie'), 'priority' => '0.6'];
    $urls[] = ['loc' => route('locaties'), 'priority' => '0.6'];

    foreach ($categories as $cat) {
        $urls[] = ['loc' => route('category.show', $cat->slug), 'priority' => '0.7'];
    }

    foreach ($products as $p) {
        $urls[] = ['loc' => route('product.show', $p->slug), 'lastmod' => $p->updated_at->toAtomString(), 'priority' => '0.8'];
    }

    $xml = view('sitemap', compact('urls'))->render();

    return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
});
