<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'type' => 'nullable|in:kachel,vloeistof,pellet,accessoire',
        ]);

        $data['slug'] = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('toast', 'Categorie aangemaakt');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'type' => 'nullable|in:kachel,vloeistof,pellet,accessoire',
        ]);

        $data['slug'] = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);

        DB::transaction(function () use ($category, $data) {
            $category->update($data);

            // De categorie is de enige bron voor het technische producttype.
            $category->products()->update(['type' => $data['type'] ?? null]);
        });

        return redirect()
            ->route('admin.categories.index')
            ->with('toast', 'Categorie en gekoppelde producten bijgewerkt');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('toast', 'Kan niet verwijderen: er zijn nog producten aan deze categorie gekoppeld.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('toast', 'Categorie verwijderd');
    }
}
