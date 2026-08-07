@extends('admin.layouts.app')

@section('title', 'Nieuw product')

@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Nieuw product</h1>
</div>

@if($errors->any())
    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <strong>Let op:</strong> Controleer de onderstaande velden.
    </div>
@endif

<form
    method="POST"
    action="{{ route('admin.products.store') }}"
    enctype="multipart/form-data"
    class="max-w-3xl space-y-5"
    x-data="{
        slugEdited: {{ old('slug') ? 'true' : 'false' }},
        imagePreview: null,
        categoryTypes: @json($categories->pluck('type', 'id')),
        selectedCategoryId: '{{ old('category_id') }}',
        get categoryType() { return this.categoryTypes[this.selectedCategoryId] || null; },
        typeLabels: { kachel: 'Kachel', vloeistof: 'Vloeistof', pellet: 'Pellet', accessoire: 'Accessoire' },
        generateSlug(val) {
            if (!this.slugEdited) {
                this.$refs.slug.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            }
        }
    }"
>
    @csrf

    @include('admin.products._image-upload')

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400">Basisinformatie</h2>

        {{-- Naam --}}
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Naam <span class="text-red-500">*</span></label>
            <input
                name="name"
                @input="generateSlug($event.target.value)"
                value="{{ old('name') }}"
                required
                class="w-full rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                placeholder="Bijv. GTL Kachelbrandstof 20L"
            >
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Slug --}}
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">URL-slug <x-admin.field-help text="Het leesbare laatste deel van het webadres. Bijvoorbeeld ‘zibro-lc-150’ in /product/zibro-lc-150. Laat dit bij voorkeur automatisch invullen." /></label>
            <div class="flex rounded-lg border border-gray-200 overflow-hidden focus-within:ring-2 focus-within:ring-green-500">
                <span class="px-3 py-2.5 bg-gray-50 text-xs text-gray-400 border-r border-gray-200 whitespace-nowrap">/product/</span>
                <input
                    name="slug"
                    x-ref="slug"
                    @input="slugEdited = true"
                    value="{{ old('slug') }}"
                    class="flex-1 px-3 py-2.5 text-sm focus:outline-none"
                    placeholder="automatisch gegenereerd"
                >
            </div>
        </div>

        {{-- Categorie --}}
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Categorie <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-3">
                <select name="category_id" @change="selectedCategoryId = $event.target.value" required class="flex-1 rounded-lg border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->has('category_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                    <option value="">— Kies categorie —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <span x-show="categoryType" x-cloak
                    class="shrink-0 inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold border"
                    :class="{
                        'bg-orange-50 text-orange-700 border-orange-200': categoryType === 'kachel',
                        'bg-blue-50 text-blue-700 border-blue-200': categoryType === 'vloeistof',
                        'bg-green-50 text-green-700 border-green-200': categoryType === 'pellet',
                        'bg-purple-50 text-purple-700 border-purple-200': categoryType === 'accessoire',
                    }"
                    x-text="typeLabels[categoryType] || categoryType"
                ></span>
            </div>
            @error('category_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Merk + Branderstype --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Merk</label>
                <input name="brand" value="{{ old('brand') }}" list="brand-suggestions"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Bijv. Zibro, Qlima">
                <datalist id="brand-suggestions">
                    @foreach($existingBrands as $b)
                        <option value="{{ $b }}">
                    @endforeach
                </datalist>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Branderstype</label>
                <input name="model_type" value="{{ old('model_type') }}" list="model-type-suggestions"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Bijv. laser, kous, gevel">
                <datalist id="model-type-suggestions">
                    @foreach($existingModelTypes as $m)
                        <option value="{{ $m }}">
                    @endforeach
                </datalist>
            </div>
        </div>

        {{-- Prijs --}}
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Prijs (€) <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">€</span>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="price"
                    value="{{ old('price') }}"
                    required
                    class="w-full pl-7 pr-3 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->has('price') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                    placeholder="0.00"
                >
            </div>
            @error('price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Korte omschrijving --}}
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Korte omschrijving</label>
            <textarea
                name="short_description"
                rows="3"
                maxlength="500"
                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Vat het belangrijkste van het product samen in enkele zinnen."
            >{{ old('short_description') }}</textarea>
            <p class="text-xs text-gray-400">Deze tekst staat direct naast de productfoto. Maximaal 500 tekens.</p>
            @error('short_description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Uitgebreide informatie --}}
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Uitgebreide productinformatie</label>
            <textarea
                name="description"
                rows="7"
                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Optionele aanvullende uitleg, gebruikstips en voordelen."
            >{{ old('description') }}</textarea>
            <p class="text-xs text-gray-400">Houd dit overzichtelijk met korte alinea’s. Technische gegevens horen bij Productspecificaties.</p>
        </div>
    </div>

    @include('admin.products._specifications')

    @include('admin.products._tier-pricing')

    {{-- Instellingen --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3">
        <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-3">Instellingen</h2>

        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" name="active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
            <div>
                <span class="text-sm font-medium text-gray-700">Actief</span>
                <p class="text-xs text-gray-400">Zichtbaar in de webshop</p>
            </div>
        </label>

        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" name="featured" value="1" @checked(old('featured')) class="h-4 w-4 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
            <div>
                <span class="text-sm font-medium text-gray-700">Uitgelicht</span>
                <p class="text-xs text-gray-400">Getoond op de homepage</p>
            </div>
        </label>

        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" name="used" value="1" @checked(old('used')) class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
            <div>
                <span class="text-sm font-medium text-gray-700">Gebruikt</span>
                <p class="text-xs text-gray-400">Tweedehands / gebruikt product</p>
            </div>
        </label>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <button type="submit" class="flex-1 sm:flex-none px-8 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 text-sm">
            Product opslaan
        </button>
        <a href="{{ route('admin.products.index') }}" class="flex-1 sm:flex-none px-8 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-center font-semibold hover:bg-gray-50 text-sm">
            Annuleren
        </a>
    </div>

</form>
@endsection
