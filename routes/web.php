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
