<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'type'        => 'nullable|string|max:100',
            'active'      => 'boolean',
        ]);

        Product::create($request->all());

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
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'type'        => 'nullable|string|max:100',
            'active'      => 'boolean',
        ]);

        $product->update($request->all());

        return redirect()
            ->route('admin.products.index')
            ->with('toast', 'Product bijgewerkt');
    }
}
