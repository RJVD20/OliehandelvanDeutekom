@extends('admin.layouts.app')

@section('title', 'Nieuw product')

@section('content')
<h1 class="text-2xl font-bold mb-6">Nieuw product</h1>

<form
    method="POST"
    action="{{ route('admin.products.store') }}"
    enctype="multipart/form-data"
    class="max-w-3xl space-y-5 rounded-lg bg-white p-5 shadow"
>
    @csrf

    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">Naam</label>
        <input
            name="name"
            value="{{ old('name') }}"
            required
            class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
        >
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">Categorie</label>
        <select name="category_id" required class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
            <option value="">-- Kies categorie --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">Type</label>
        <select name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
            @foreach(['kachel','vloeistof','pellet','accessoire'] as $type)
                <option value="{{ $type }}" @selected(old('type') === $type)>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">Prijs (€)</label>
        <input
            type="number"
            step="0.01"
            name="price"
            value="{{ old('price') }}"
            required
            class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
        >
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">Beschrijving</label>
        <textarea
            name="description"
            rows="4"
            class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
        >{{ old('description') }}</textarea>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">Productafbeelding</label>
        <input
            type="file"
            name="image"
            accept="image/*"
            class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
        >
    </div>

    <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-sm font-medium text-gray-800">
        <input type="checkbox" name="active" value="1" checked class="h-5 w-5 rounded border-gray-300">
        <span>Actief</span>
    </label>

    <div class="flex flex-col sm:flex-row gap-3">
        <button class="w-full sm:w-auto px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">
            Opslaan
        </button>

        <a href="{{ route('admin.products.index') }}"
           class="w-full sm:w-auto px-6 py-3 border rounded-lg text-center font-semibold text-gray-800">
            Annuleren
        </a>
    </div>
</form>
@endsection
