<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\ShippingRates;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Product::with('category');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        match($request->get('sort')) {
            'name_asc'   => $query->orderBy('name'),
            'name_desc'  => $query->orderByDesc('name'),
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default      => $query->latest(),
        };

        return view('admin.products.index', [
            'products'         => $query->paginate(20)->withQueryString(),
            'categories'       => Category::orderBy('name')->get(),
            'totalProducts'    => Product::count(),
            'activeProducts'   => Product::where('active', true)->count(),
            'inactiveProducts' => Product::where('active', false)->count(),
            'totalOrders'      => Order::placed()->count(),
        ]);
    }

    public function create()
    {
        $categories        = Category::orderBy('name')->get();
        $existingBrands    = Product::whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');
        $existingModelTypes = Product::whereNotNull('model_type')->distinct()->orderBy('model_type')->pluck('model_type');
        return view('admin.products.create', compact('categories', 'existingBrands', 'existingModelTypes'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $tierData = $this->extractTierData($data);
        $data['slug']     = $request->filled('slug') ? \Illuminate\Support\Str::slug($request->input('slug')) : \Illuminate\Support\Str::slug($data['name']);
        $data['active']   = $request->boolean('active');
        $data['featured'] = $request->boolean('featured');
        $data['used']     = $request->boolean('used');
        $categoryType = Category::find($data['category_id'])?->type;
        if ($categoryType !== null) {
            $data['type'] = $categoryType;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        app(ShippingRates::class)->saveProductRule($product->id, ...$tierData);

        return redirect()
            ->route('admin.products.index')
            ->with('toast', 'Product aangemaakt');
    }

    public function edit(Product $product)
    {
        $categories        = Category::orderBy('name')->get();
        $existingBrands    = Product::whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');
        $existingModelTypes = Product::whereNotNull('model_type')->distinct()->orderBy('model_type')->pluck('model_type');
        $productRule = app(ShippingRates::class)->ruleForProduct($product->id);
        return view('admin.products.edit', compact('product', 'categories', 'existingBrands', 'existingModelTypes', 'productRule'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $tierData = $this->extractTierData($data);
        $data['slug']     = $request->filled('slug') ? \Illuminate\Support\Str::slug($request->input('slug')) : \Illuminate\Support\Str::slug($data['name']);
        $data['active']   = $request->boolean('active');
        $data['featured'] = $request->boolean('featured');
        $data['used']     = $request->boolean('used');
        $categoryType = Category::find($data['category_id'])?->type;
        if ($categoryType !== null) {
            $data['type'] = $categoryType;
        }

        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        app(ShippingRates::class)->saveProductRule($product->id, ...$tierData);

        return redirect()
            ->route('admin.products.index')
            ->with('toast', 'Product bijgewerkt');
    }

    public function destroy(Product $product)
    {
        app(ShippingRates::class)->saveProductRule($product->id, false);
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
            'active' => $product->active,
        ]);
    }

    public function toggleFeatured(Product $product)
    {
        $product->featured = ! (bool) $product->featured;
        $product->save();

        return response()->json([
            'featured' => $product->featured,
        ]);
    }

    private function extractTierData(array &$data): array
    {
        $enabled = (bool) ($data['tier_pricing_enabled'] ?? false);
        $delivery = $data['delivery_tiers'] ?? [];
        $pickup = $data['pickup_tiers'] ?? [];

        unset(
            $data['tier_pricing_enabled'],
            $data['delivery_tiers'],
            $data['pickup_tiers'],
        );

        return [$enabled, $delivery, $pickup];
    }
}
