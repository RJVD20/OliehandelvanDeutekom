<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;

class ProductController extends Controller
{
public function index()
{
    return view('admin.products.index', [
        'products'         => Product::latest()->paginate(20),
        'totalProducts'    => Product::count(),
        'activeProducts'   => Product::where('active', true)->count(),
        'inactiveProducts' => Product::where('active', false)->count(),
        'totalOrders'      => Order::count(),
    ]);
}


    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
$data = $request->validate([
    'name'        => 'required|string|max:255',
    'price'       => 'required|numeric',
    'category_id' => 'required|exists:categories,id',
    'type'        => 'nullable|string|max:100',
    'description' => 'nullable|string',
    'image'       => 'nullable|image|max:2048',
    'active'      => 'sometimes|boolean',
]);

$data['slug']   = \Str::slug($data['name']);
$data['active'] = $request->boolean('active');

if ($request->hasFile('image')) {
    $data['image'] = $request->file('image')->store('products', 'public');
}

Product::create($data);


        return redirect()
            ->route('admin.products.index')
            ->with('toast', 'Product aangemaakt');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'type'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'active'      => 'sometimes|boolean',
        ]);

        $data['slug']   = \Str::slug($data['name']);
        $data['active'] = $request->boolean('active');

        if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
}


        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('toast', 'Product bijgewerkt');
    }


    public function destroy(Product $product)
{
    $product->delete();

    return redirect()
        ->route('admin.products.index')
        ->with('toast', 'Product verwijderd');
}

public function toggleActive(Product $product)
{
    $product->active = ! (bool) $product->active;
    $product->save();

    return response()->json([
        'active' => $product->active
    ]);
}

public function toggleFeatured(Product $product)
{
    $product->featured = ! (bool) $product->featured;
    $product->save();

    return response()->json([
        'featured' => $product->featured
    ]);
}


}
