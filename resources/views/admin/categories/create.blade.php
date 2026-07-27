@extends('admin.layouts.app')

@section('title', 'Nieuwe categorie')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Nieuwe categorie</h1>
</div>

<form
    method="POST"
    action="{{ route('admin.categories.store') }}"
    class="max-w-xl"
    x-data="{
        name: '',
        slug: '',
        slugEdited: false,
        generateSlug(val) {
            if (!this.slugEdited) {
                this.slug = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            }
        }
    }"
>
    @csrf

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Naam <span class="text-red-500">*</span></label>
            <input
                name="name"
                x-model="name"
                @input="generateSlug($event.target.value)"
                value="{{ old('name') }}"
                required
                autofocus
                class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                placeholder="Bijv. Pelletkachels"
            >
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Type</label>
            <select name="type" class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">— Geen type —</option>
                @foreach(['kachel' => 'Kachel', 'vloeistof' => 'Vloeistof', 'pellet' => 'Pellet', 'accessoire' => 'Accessoire'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400">Bepaalt onder welk producttype deze categorie valt en wordt automatisch overgenomen door producten in deze categorie.</p>
            @error('type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">URL-slug</label>
            <div class="flex rounded-lg border border-gray-200 overflow-hidden focus-within:ring-2 focus-within:ring-green-500">
                <span class="px-3 py-2.5 bg-gray-50 text-xs text-gray-400 border-r border-gray-200 whitespace-nowrap">/categorie/</span>
                <input
                    name="slug"
                    x-model="slug"
                    @input="slugEdited = true"
                    class="flex-1 px-3 py-2.5 text-sm focus:outline-none"
                    placeholder="automatisch gegenereerd"
                >
            </div>
            <p class="text-xs text-gray-400">Wordt automatisch ingevuld op basis van de naam.</p>
            @error('slug') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

    </div>

    <div class="mt-4 flex gap-3">
        <button type="submit"
            class="px-8 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 text-sm">
            Categorie opslaan
        </button>
        <a href="{{ route('admin.categories.index') }}"
            class="px-8 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-center font-semibold hover:bg-gray-50 text-sm">
            Annuleren
        </a>
    </div>
</form>

@endsection
