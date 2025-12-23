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
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;

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
| Winkelmand
|--------------------------------------------------------------------------
*/

Route::get('/cart', function () {
    $cart = session('cart', []);
    return view('themes.default.pages.cart', compact('cart'));
})->name('cart.index');

Route::post('/cart/add/{id}', function ($id) {

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

Route::post('/cart/update/{id}', function (Request $request, $id) {

    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = max(1, (int) $request->quantity);
        session()->put('cart', $cart);
    }

    return back();
})->name('cart.update');

Route::post('/cart/remove/{id}', function ($id) {

    $cart = session()->get('cart', []);
    unset($cart[$id]);
    session()->put('cart', $cart);

    return back();
})->name('cart.remove');

/*
|--------------------------------------------------------------------------
| Checkout (guest + ingelogd)
|--------------------------------------------------------------------------
*/

Route::get('/checkout', function () {
    $cart = session('cart', []);
    return view('themes.default.pages.checkout', compact('cart'));
})->name('checkout.index');

Route::post('/checkout', function (Request $request) {

    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email',
        'address'  => 'required|string|max:255',
        'postcode' => ['required', 'regex:/^[1-9][0-9]{3}\s?[A-Z]{2}$/i'],
        'city'     => 'required|string|max:255',
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
    ]);

    foreach ($cart as $productId => $item) {
        $order->items()->create([
            'product_id'   => $productId,
            'product_name' => $item['name'],
            'price'        => $item['price'],
            'quantity'     => $item['quantity'],
        ]);
    }

    if (auth()->check()) {
        auth()->user()->update([
            'address'  => $request->address,
            'postcode' => $postcode,
            'city'     => $request->city,
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Redirect legacy /account to profile edit (users edit their profile directly)
    Route::get('/account', fn () => redirect()->route('profile.edit'))
        ->name('account.dashboard');

    Route::get('/account/orders', fn () => view('account.orders'))->name('account.orders');

    Route::get('/account/orders/{order}', function (Order $order) {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('account.order-show', compact('order'));
    })->name('account.orders.show');

    // Re-order: place the same order again
    Route::post('/account/orders/{order}/reorder', function (Order $order) {
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
    Route::get('/account/orders/{order}/invoice', function (Order $order) {
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

        // Orders
        Route::get('/orders', function () {
            $orders = Order::latest()->paginate(20);
            return view('admin.orders.index', compact('orders'));
        })->name('orders.index');

        Route::get('/orders/{order}', function (Order $order) {
            return view('admin.orders.show', compact('order'));
        })->name('orders.show');

        Route::post('/orders/{order}/ship', function (Order $order) {
            $order->update(['status' => 'shipped']);

            Mail::to($order->email)->send(new OrderShippedMail($order));

            return back()->with('toast', 'Verzendmail verstuurd');
        })->name('orders.ship');

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
    });

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
