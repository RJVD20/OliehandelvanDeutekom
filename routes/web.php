<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\ProfileController;

use App\Mail\OrderConfirmationMail;

use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use App\Models\Order;

/*
|--------------------------------------------------------------------------
| Publieke pagina’s
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $products = Product::where('active', true)->take(8)->get();
    $categories = Category::all();

    return view('themes.default.pages.home', compact('products', 'categories'));
})->name('home');

Route::get('/informatie', function () {
    return view('themes.default.pages.informatie');
})->name('informatie');

Route::get('/locaties', function () {
    $locaties = Location::orderBy('name')->get();
    return view('themes.default.pages.locaties', compact('locaties'));
})->name('locaties');


/*
|--------------------------------------------------------------------------
| Producten
|--------------------------------------------------------------------------
*/

Route::get('/product/{slug}', function ($slug) {
    $product = Product::where('slug', $slug)->firstOrFail();
    return view('themes.default.pages.product', compact('product'));
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

    $products = $query->paginate(12)->withQueryString();
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

    // Postcode normaliseren
    $postcode = strtoupper(str_replace(' ', '', $request->postcode));
    $postcode = substr($postcode, 0, 4) . ' ' . substr($postcode, 4);

    $cart = session('cart', []);
    abort_if(empty($cart), 400);

    $total = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

    $order = Order::create([
        'user_id' => auth()->id(), // null bij guest
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

    // Alleen adres onthouden als user is ingelogd
    if (auth()->check()) {
        auth()->user()->update([
            'address'  => $request->address,
            'postcode' => $postcode,
            'city'     => $request->city,
        ]);
    }

    Mail::to($order->email)->send(new OrderConfirmationMail($order));

    session()->forget('cart');

if (auth()->check()) {
    return redirect()
        ->route('account.orders')
        ->with('toast', 'Bestelling geplaatst 🎉');
}

return redirect()
    ->route('home')
    ->with('toast', 'Bestelling geplaatst 🎉 Check je e-mail voor de bevestiging.');


})->name('checkout.store');


/*
|--------------------------------------------------------------------------
| Account & profiel (alleen ingelogd)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/account', function () {
        return view('account.dashboard');
    })->name('account.dashboard');

    Route::get('/account/orders', function () {
        return view('account.orders');
    })->name('account.orders');

    Route::get('/account/orders/{order}', function (Order $order) {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('account.order-show', compact('order'));
    })->name('account.orders.show');

    Route::get('/dashboard', function () {
        return redirect()->route('account.dashboard');
    })->name('dashboard');
});

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/orders', function () {
        $orders = Order::latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    })->name('admin.orders.index');

    Route::get('/orders/{order}', function (Order $order) {
        return view('admin.orders.show', compact('order'));
    })->name('admin.orders.show');

});

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
