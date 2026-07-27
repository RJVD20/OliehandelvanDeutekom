@extends('admin.layouts.app')

@section('title', 'Producten')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-500 mb-1">Totaal</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-green-100 p-4">
        <p class="text-xs text-green-600 mb-1">Actief</p>
        <p class="text-2xl font-bold text-green-700">{{ $activeProducts }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-500 mb-1">Inactief</p>
        <p class="text-2xl font-bold text-gray-400">{{ $inactiveProducts }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-500 mb-1">Bestellingen</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalOrders }}</p>
    </div>
</div>

{{-- Header + New button --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Producten</h1>
    <a
        href="{{ route('admin.products.create') }}"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nieuw product
    </a>
</div>

{{-- Search & Filters --}}
<form method="GET" action="{{ route('admin.products.index') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="lg:col-span-2">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 6a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z"/></svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Zoek op naam…"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                >
            </div>
        </div>

        <select name="category_id" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-green-500" onchange="this.form.submit()">
            <option value="">Alle categorieën</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="type" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-green-500" onchange="this.form.submit()">
            <option value="">Alle types</option>
            @foreach(['kachel','vloeistof','pellet','accessoire'] as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-3">
        <select name="sort" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-green-500" onchange="this.form.submit()">
            <option value="">Sortering: nieuwste eerst</option>
            <option value="name_asc"   @selected(request('sort') === 'name_asc')>Naam A→Z</option>
            <option value="name_desc"  @selected(request('sort') === 'name_desc')>Naam Z→A</option>
            <option value="price_asc"  @selected(request('sort') === 'price_asc')>Prijs laag→hoog</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>Prijs hoog→laag</option>
        </select>

        <button type="submit" class="px-4 py-2 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-medium">Zoeken</button>

        @if(request()->hasAny(['search','category_id','type','sort']))
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">× Filters wissen</a>
        @endif

        <span class="ml-auto text-xs text-gray-400">{{ $products->total() }} producten</span>
    </div>
</form>

{{-- Desktop table --}}
<div class="hidden md:block bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="px-4 py-3 text-left w-14"></th>
                <th class="px-4 py-3 text-left">Naam</th>
                <th class="px-4 py-3 text-left">Categorie</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-right">Prijs</th>
                <th class="px-4 py-3 text-center">Actief</th>
                <th class="px-4 py-3 text-center">Uitgelicht</th>
                <th class="px-4 py-3 text-right">Acties</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition-colors" x-data="{ confirmDelete: false }">
                    {{-- Thumbnail --}}
                    <td class="px-4 py-3">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="" class="h-10 w-10 rounded-lg object-contain border border-gray-100 bg-gray-50">
                        @else
                            <div class="h-10 w-10 rounded-lg border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-gray-300 text-xs">—</div>
                        @endif
                    </td>

                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $product->name }}
                        <span class="block text-xs text-gray-400 font-normal">{{ $product->slug }}</span>
                    </td>

                    <td class="px-4 py-3 text-gray-600">{{ $product->category->name ?? '—' }}</td>

                    <td class="px-4 py-3 text-gray-500">{{ $product->type ? ucfirst($product->type) : '—' }}</td>

                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                        € {{ number_format($product->price, 2, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <button
                            type="button"
                            onclick="toggleActive({{ $product->id }}, this)"
                            class="px-2.5 py-1 text-xs rounded-full font-medium transition
                                {{ $product->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}"
                        >
                            {{ $product->active ? 'Actief' : 'Inactief' }}
                        </button>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <button
                            type="button"
                            onclick="toggleFeatured({{ $product->id }}, this)"
                            class="px-2.5 py-1 text-xs rounded-full font-medium transition
                                {{ $product->featured ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}"
                        >
                            {{ $product->featured ? '★ Uitgelicht' : '☆ Niet' }}
                        </button>
                    </td>

                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Bewerken</a>

                            <div x-show="!confirmDelete">
                                <button type="button" @click="confirmDelete = true" class="text-sm text-red-500 hover:text-red-700 font-medium">Verwijderen</button>
                            </div>

                            <div x-show="confirmDelete" x-cloak class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Zeker?</span>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Ja</button>
                                </form>
                                <button type="button" @click="confirmDelete = false" class="text-xs font-semibold text-gray-500 hover:text-gray-700">Nee</button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                        Geen producten gevonden.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile cards --}}
<div class="md:hidden space-y-3">
    @forelse($products as $product)
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm" x-data="{ confirmDelete: false }">
            <div class="flex items-start gap-3">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="" class="h-14 w-14 rounded-lg object-contain border border-gray-100 bg-gray-50 shrink-0">
                @else
                    <div class="h-14 w-14 rounded-lg border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-gray-300 shrink-0">—</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold leading-tight text-gray-800">{{ $product->name }}</p>
                    <p class="text-xs text-gray-400">{{ $product->category->name ?? 'Geen categorie' }} · {{ $product->type ? ucfirst($product->type) : '' }}</p>
                    <p class="text-base font-bold text-green-700 mt-1">€ {{ number_format($product->price, 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <button type="button" onclick="toggleActive({{ $product->id }}, this)"
                    class="px-3 py-1.5 text-xs rounded-full font-medium {{ $product->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $product->active ? 'Actief' : 'Inactief' }}
                </button>
                <button type="button" onclick="toggleFeatured({{ $product->id }}, this)"
                    class="px-3 py-1.5 text-xs rounded-full font-medium {{ $product->featured ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $product->featured ? '★ Uitgelicht' : '☆ Niet' }}
                </button>
            </div>

            <div class="mt-3 flex items-center justify-between gap-2">
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="flex-1 text-center rounded-lg bg-gray-800 px-3 py-2 text-white text-sm font-semibold hover:bg-gray-700">
                    Bewerken
                </a>

                <div x-show="!confirmDelete" class="flex-1">
                    <button type="button" @click="confirmDelete = true"
                        class="w-full rounded-lg border border-red-200 px-3 py-2 text-red-600 text-sm font-semibold hover:bg-red-50">
                        Verwijderen
                    </button>
                </div>

                <div x-show="confirmDelete" x-cloak class="flex-1 flex items-center justify-center gap-3">
                    <span class="text-xs text-gray-500">Zeker?</span>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm font-semibold text-red-600">Ja</button>
                    </form>
                    <button type="button" @click="confirmDelete = false" class="text-sm font-semibold text-gray-500">Nee</button>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-gray-100 bg-white p-8 text-center text-gray-400">
            Geen producten gevonden.
        </div>
    @endforelse
</div>

<div class="mt-5">{{ $products->links() }}</div>

@endsection

@section('scripts')
<script>
function toggleActive(productId, button) {
    fetch(`/admin/products/${productId}/toggle-active`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.active) {
            button.textContent = 'Actief';
            button.className = 'px-2.5 py-1 text-xs rounded-full font-medium transition bg-green-100 text-green-700';
        } else {
            button.textContent = 'Inactief';
            button.className = 'px-2.5 py-1 text-xs rounded-full font-medium transition bg-gray-100 text-gray-500';
        }
    })
    .catch(() => alert('Status wijzigen mislukt'));
}

function toggleFeatured(productId, button) {
    fetch(`/admin/products/${productId}/toggle-featured`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.featured) {
            button.textContent = '★ Uitgelicht';
            button.className = 'px-2.5 py-1 text-xs rounded-full font-medium transition bg-yellow-100 text-yellow-700';
        } else {
            button.textContent = '☆ Niet';
            button.className = 'px-2.5 py-1 text-xs rounded-full font-medium transition bg-gray-100 text-gray-500';
        }
    })
    .catch(() => alert('Wijzigen mislukt'));
}
</script>
@endsection
