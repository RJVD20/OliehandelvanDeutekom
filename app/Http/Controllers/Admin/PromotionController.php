<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('mainProduct')->orderBy('sort_order')->latest()->paginate(25);
        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.form', [
            'promotion' => new Promotion(['show_home' => true, 'show_product' => true, 'show_cart' => true]),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $promotion = DB::transaction(function () use ($request, $data) {
            $items = $data['items'] ?? [];
            unset($data['items']);
            $data = $this->normalize($request, $data);
            $promotion = Promotion::create($data);
            $this->syncItems($promotion, $items);
            return $promotion;
        });
        AuditLog::record('created', 'promotion', $promotion->id, $promotion->name, [], $promotion->toArray());
        return redirect()->route('admin.promotions.edit', $promotion)->with('toast', 'Actie aangemaakt.');
    }

    public function edit(Promotion $promotion)
    {
        $promotion->load('items.product');
        return view('admin.promotions.form', [
            'promotion' => $promotion,
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Promotion $promotion)
    {
        $before = $promotion->toArray();
        $data = $this->validated($request, $promotion);
        DB::transaction(function () use ($request, $data, $promotion) {
            $items = $data['items'] ?? [];
            unset($data['items']);
            $promotion->update($this->normalize($request, $data, $promotion));
            $this->syncItems($promotion, $items);
        });
        AuditLog::record('updated', 'promotion', $promotion->id, $promotion->name, $before, $promotion->fresh()->toArray());
        return back()->with('toast', 'Actie opgeslagen.');
    }

    public function destroy(Promotion $promotion)
    {
        AuditLog::record('deleted', 'promotion', $promotion->id, $promotion->name, $promotion->toArray(), []);
        $promotion->delete();
        return redirect()->route('admin.promotions.index')->with('toast', 'Actie verwijderd.');
    }

    private function validated(Request $request, ?Promotion $promotion = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('promotions')->ignore($promotion)],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'main_product_id' => ['required', Rule::exists('products', 'id')->where('active', true)],
            'fixed_price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'image' => ['nullable', 'image', 'max:8192'],
            'existing_image_path' => ['nullable', 'string', 'max:500'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array', 'max:20'],
            'items.*.product_id' => ['required', 'distinct', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.role' => ['required', Rule::in(['included', 'free'])],
            'items.*.label' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function normalize(Request $request, array $data, ?Promotion $promotion = null): array
    {
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        foreach (['active', 'free_shipping', 'show_home', 'show_product', 'show_cart'] as $field) {
            $data[$field] = $request->boolean($field);
        }
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['image_path'] = $data['existing_image_path'] ?? $promotion?->image_path;
        unset($data['existing_image_path'], $data['image']);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('promotions', 'public');
        }
        return $data;
    }

    private function syncItems(Promotion $promotion, array $items): void
    {
        $promotion->items()->delete();
        foreach (array_values($items) as $index => $item) {
            if ((int) $item['product_id'] === (int) $promotion->main_product_id) continue;
            $promotion->items()->create([...$item, 'sort_order' => $index]);
        }
    }
}
