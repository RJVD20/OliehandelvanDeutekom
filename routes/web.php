<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

Route::get('/', function () {
    $products = Product::where('active', true)->take(8)->get();
    $categories = Category::all();

    return view('themes.default.pages.home', compact('products', 'categories'));
});

Route::get('/product/{slug}', function ($slug) {
    $product = Product::where('slug', $slug)->firstOrFail();
    return view('themes.default.pages.product', compact('product'));
})->name('product.show');

Route::get('/categories/{slug}', function ($slug) {
    $category = Category::where('slug', $slug)->firstOrFail();
    $products = $category->products()->where('active', true)->paginate(12);

    return view('themes.default.pages.category', compact('category', 'products'));
})->name('category.show');

Route::post('/cart/add/{id}', function ($id) {
    $product = \App\Models\Product::findOrFail($id);

    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
        ];
    }

    session()->put('cart', $cart);

    // 🔥 FLASH MESSAGE
    session()->flash('toast', 'Product toegevoegd aan winkelmand');

    return back();
})->name('cart.add');


Route::get('/cart', function () {
    $cart = session('cart', []);
    return view('themes.default.pages.cart', compact('cart'));
})->name('cart.index');

Route::post('/cart/remove/{id}', function ($id) {
    $cart = session()->get('cart', []);
    unset($cart[$id]);
    session()->put('cart', $cart);

    return back();
})->name('cart.remove');

Route::post('/cart/update/{id}', function (Illuminate\Http\Request $request, $id) {
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = max(1, (int) $request->quantity);
        session()->put('cart', $cart);
    }

    return back();
})->name('cart.update');

Route::get('/checkout', function () {
    $cart = session('cart', []);
    return view('themes.default.pages.checkout', compact('cart'));
})->name('checkout.index');


Route::get('/informatie', function () {
    return view('themes.default.pages.informatie');
})->name('informatie');


Route::get('/producten', function (Request $request) {

    $query = Product::where('active', true);

    // ✅ Categorie filter (MOET BOVENAAN)
    if ($request->filled('categories')) {
        $query->whereIn('category_id', $request->categories);
    }

    // Type filter
    if ($request->filled('types')) {
        $query->whereIn('type', $request->types);
    }

    // Prijs filter
    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Sortering
    if ($request->filled('sort')) {
        match ($request->sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest' => $query->latest(),
            default => null,
        };
    }

    // ❗ PAS HIER PAGINATE
    $products = $query->paginate(12)->withQueryString();
    $categories = Category::all();

    return view('themes.default.pages.products.index', compact('products', 'categories'));
})->name('products.index');

use App\Models\Location;

Route::get('/locaties', function () {

    $locaties = Location::orderBy('name')->get();

    return view('themes.default.pages.locaties', compact('locaties'));

})->name('locaties');
