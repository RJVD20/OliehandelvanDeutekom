@extends('admin.layouts.app')

@section('title', 'Categorie bewerken')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Categorie bewerken</h1>
</div>

<form
    method="POST"
    action="{{ route('admin.categories.update', $category) }}"
    class="max-w-xl"
    x-data="{
        name: '{{ old('name', $category->name) }}',
        slug: '{{ old('slug', $category->slug) }}',
        slugEdited: true
    }"
>
    @csrf
    @method('PUT')

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Naam <span class="text-red-500">*</span></label>
            <input
                name="name"
                x-model="name"
                required
                class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
            >
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Type</label>
            <select name="type" class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">— Geen type —</option>
                @foreach(['kachel' => 'Kachel', 'vloeistof' => 'Vloeistof', 'pellet' => 'Pellet', 'accessoire' => 'Accessoire'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $category->type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400">Het type wordt automatisch overgenomen door alle producten in deze categorie.</p>
            @error('type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">URL-slug</label>
            <div class="flex rounded-lg border border-gray-200 overflow-hidden focus-within:ring-2 focus-within:ring-green-500">
                <span class="px-3 py-2.5 bg-gray-50 text-xs text-gray-400 border-r border-gray-200 whitespace-nowrap">/categorie/</span>
                <input
                    name="slug"
                    x-model="slug"
                    class="flex-1 px-3 py-2.5 text-sm focus:outline-none"
                >
            </div>
            <p class="text-xs text-amber-600">⚠ De slug aanpassen verbreekt bestaande links naar deze categorie.</p>
            @error('slug') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        @if($category->products_count > 0 ?? $category->products()->count() > 0)
            <div class="rounded-lg bg-blue-50 border border-blue-100 px-4 py-3 text-sm text-blue-700">
                Deze categorie heeft <strong>{{ $category->products()->count() }} producten</strong>.
                Verwijderen is pas mogelijk als alle producten een andere categorie hebben.
            </div>
        @endif

    </div>

    <div class="mt-4 flex gap-3">
        <button type="submit"
            class="px-8 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 text-sm">
            Wijzigingen opslaan
        </button>
        <a href="{{ route('admin.categories.index') }}"
            class="px-8 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-center font-semibold hover:bg-gray-50 text-sm">
            Annuleren
        </a>
    </div>
</form>

@endsection
