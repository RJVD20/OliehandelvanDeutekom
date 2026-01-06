@extends('themes.default.layouts.app')

@section('title', 'Alle producten')

@section('content')

<h1 class="text-3xl font-bold text-green-700 mb-8">
    Alle producten
</h1>

<div
    x-data="{ mobileFilters: false }"
    @keydown.escape.window="mobileFilters = false"
    class="space-y-0"
>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8">

    <!-- FILTER SIDEBAR -->
    <aside class="hidden lg:block lg:col-span-1">

        <form
            method="GET"
            action="{{ route('products.index') }}"
            x-data="{
                min: {{ request('min_price', 0) }},
                max: {{ request('max_price', 500) }}
            }"
            @change="$el.submit()"
            class="space-y-8"
        >

            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <!-- CATEGORIE -->
            <div>
                <h3 class="font-semibold mb-3">Categorie</h3>
                <ul class="space-y-2 text-sm">
                    @foreach ($categories as $category)
                        <li>
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="{{ $category->id }}"
                                    @checked(in_array((string) $category->id, request('categories', [])))
                                    class="accent-green-600"
                                >
                                {{ $category->name }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- TYPE -->
            <div>
                <h3 class="font-semibold mb-3">Type</h3>
                <ul class="space-y-2 text-sm">
                    @foreach (['kachel','vloeistof','pellet','accessoire'] as $type)
                        <li>
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="types[]"
                                    value="{{ $type }}"
                                    @checked(in_array($type, request('types', [])))
                                    class="accent-green-600"
                                >
                                {{ ucfirst($type) }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- PRIJS -->
            <div class="space-y-3">
                <h3 class="font-semibold">Prijs</h3>

                <input type="range" min="0" max="500" step="5" x-model="min">
                <input type="range" min="0" max="500" step="5" x-model="max">

                <div class="text-sm text-gray-600">
                    € <span x-text="min"></span> - € <span x-text="max"></span>
                </div>

                <input type="hidden" name="min_price" :value="min">
                <input type="hidden" name="max_price" :value="max">
            </div>

        </form>

    </aside>

    <!-- PRODUCT GRID -->
    <section class="lg:col-span-3">

        <!-- Mobile filter trigger -->
        <div class="lg:hidden flex items-center justify-between mb-4">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:border-green-600 hover:text-green-700"
                @click="mobileFilters = true"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 6h18M6 12h12m-9 6h6" />
                </svg>
                Filters
            </button>
            <span class="text-sm text-gray-500">{{ $products->total() }} resultaten</span>
        </div>

        <!-- TOP BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">

            <p class="text-sm text-gray-500">
                {{ $products->total() }} producten gevonden
            </p>

            <!-- SORTEREN -->
            <form method="GET" action="{{ route('products.index') }}">
                {{-- filters behouden --}}
                @foreach (request()->except('sort', 'page') as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <select
                    name="sort"
                    onchange="this.form.submit()"
                    class="border rounded-lg p-2 text-sm bg-white shadow-sm"
                >
                    <option value="">Sorteren op</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>
                        Prijs: laag → hoog
                    </option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>
                        Prijs: hoog → laag
                    </option>
                    <option value="newest" @selected(request('sort') === 'newest')>
                        Nieuwste eerst
                    </option>
                </select>
            </form>

        </div>

        <!-- PRODUCTEN -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse ($products as $product)
                <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition group">

                    <a
                        href="{{ route('product.show', $product->slug) }}"
                        class="block h-44 bg-green-50 flex items-center justify-center rounded-t-lg group-hover:bg-green-100 transition"
                    >
                        <span class="text-green-700 font-semibold text-center px-3">
                            {{ $product->name }}
                        </span>
                    </a>

                    <div class="p-4">
                        <h3 class="font-semibold mb-1">
                            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-green-700">
                                {{ $product->name }}
                            </a>
                        </h3>

                        <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                            {{ $product->description }}
                        </p>

                        <div class="flex items-center justify-between">
                            <span class="font-bold text-green-700">
                                € {{ number_format($product->price, 2, ',', '.') }}
                            </span>

                            <form method="POST" action="{{ route('cart.add', $product->id) }}">
                                @csrf
                                <button class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                    In winkelmand
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-3 text-gray-500">
                    Geen producten gevonden.
                </p>
            @endforelse
        </div>

        <!-- PAGINATION -->
        <div class="mt-10">
            {{ $products->withQueryString()->links() }}
        </div>

    </section>

<div></div>

<!-- Mobile filters drawer -->
<div
    class="lg:hidden fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
    x-show="mobileFilters"
    x-transition.opacity
    @click.self="mobileFilters = false"
    style="display: none;"
>
    <div
        class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-2xl p-5 space-y-6 max-h-[80vh] overflow-y-auto"
        x-transition.origin-bottom
    >
        <div class="flex items-center justify-between">
            <div class="font-semibold text-gray-800">Filters</div>
            <button class="p-2 text-gray-500 hover:text-gray-700" @click="mobileFilters = false" aria-label="Sluit filters">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form
            method="GET"
            action="{{ route('products.index') }}"
            x-data="{
                min: {{ request('min_price', 0) }},
                max: {{ request('max_price', 500) }}
            }"
            @change="$el.submit()"
            class="space-y-6 text-sm text-gray-700"
        >
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <div class="space-y-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Categorie</div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <label class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-2 bg-gray-50 text-gray-700">
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                                @checked(in_array((string) $category->id, request('categories', [])))
                                class="accent-green-600"
                            >
                            <span class="text-sm">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Type</div>
                <div class="flex flex-wrap gap-2">
                    @foreach (['kachel','vloeistof','pellet','accessoire'] as $type)
                        <label class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-2 bg-gray-50 text-gray-700">
                            <input
                                type="checkbox"
                                name="types[]"
                                value="{{ $type }}"
                                @checked(in_array($type, request('types', [])))
                                class="accent-green-600"
                            >
                            <span class="text-sm">{{ ucfirst($type) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Prijs</div>

                <div class="flex items-center gap-3">
                    <input type="range" min="0" max="500" step="5" x-model="min" class="w-full">
                    <input type="range" min="0" max="500" step="5" x-model="max" class="w-full">
                </div>

                <div class="text-sm text-gray-600">
                    € <span x-text="min"></span> - € <span x-text="max"></span>
                </div>

                <input type="hidden" name="min_price" :value="min">
                <input type="hidden" name="max_price" :value="max">
            </div>

        </form>
    </div>
</div>
</div>

@endsection
