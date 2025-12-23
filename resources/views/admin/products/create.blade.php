@extends('admin.layouts.app')

@section('title', 'Nieuw product')

@section('content')
<h1 class="text-2xl font-bold mb-6">Nieuw product</h1>

<form method="POST"
      action="{{ route('admin.products.store') }}"
      enctype="multipart/form-data">
        @csrf

    <div>
        <label class="block font-medium mb-1">Naam</label>
        <input name="name" value="{{ old('name') }}" required
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block font-medium mb-1">Categorie</label>
        <select name="category_id" required class="w-full border rounded px-3 py-2">
            <option value="">-- Kies categorie --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block font-medium mb-1">Type</label>
        <select name="type" required class="w-full border rounded px-3 py-2">
            @foreach(['kachel','vloeistof','pellet','accessoire'] as $type)
                <option value="{{ $type }}" @selected(old('type') === $type)>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block font-medium mb-1">Prijs (€)</label>
        <input type="number" step="0.01" name="price" value="{{ old('price') }}" required
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block font-medium mb-1">Beschrijving</label>
        <textarea name="description" rows="4"
                  class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
    </div>

    <div>
        <label class="block font-medium mb-1">Productafbeelding</label>
        <input
            type="file"
            name="image"
            accept="image/*"
            class="w-full border rounded px-3 py-2">
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="active" value="1" checked>
        <label>Actief</label>
    </div>

    <div class="flex gap-4">
        <button class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Opslaan
        </button>

        <a href="{{ route('admin.products.index') }}"
           class="px-6 py-2 border rounded">
            Annuleren
        </a>
    </div>
</form>
@endsection
