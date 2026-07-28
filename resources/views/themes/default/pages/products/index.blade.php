@extends('themes.default.layouts.app')

@section('title', $title)

@section('content')

@php
    $activeFilterCount =
        count(request('categories', [])) +
        count(request('brands', [])) +
        count(request('model_types', [])) +
        (request('condition') ? 1 : 0) +
        (request('min_price', 0) != 0 || request('max_price', 500) != 500 ? 1 : 0);
@endphp

<div class="mb-6 sm:mb-8">
    <div class="rounded-2xl border border-turbo-blue/10 bg-white px-5 py-5 shadow-sm sm:px-7 sm:py-7">
        <p class="turbo-section-label mb-1.5 sm:mb-2">Assortiment</p>
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold leading-tight sm:text-3xl">{{ $title }}</h1>
                <p class="mt-1.5 text-sm text-gray-500">{{ $products->total() }} producten beschikbaar</p>
            </div>
            <div class="hidden h-12 w-1 rounded-full bg-turbo-gold sm:block"></div>
        </div>
    </div>

    <nav class="mt-3 flex gap-2 overflow-x-auto pb-1 text-sm font-semibold" aria-label="Productgroepen">
        <a href="{{ route('products.heaters') }}" class="whitespace-nowrap rounded-full border px-4 py-2 {{ request()->routeIs('products.heaters') ? 'border-turbo-gold bg-turbo-gold text-turbo-navy' : 'border-turbo-blue/15 bg-white text-turbo-ink' }}">Kachels</a>
        <a href="{{ route('products.liquids') }}" class="whitespace-nowrap rounded-full border px-4 py-2 {{ request()->routeIs('products.liquids') ? 'border-turbo-gold bg-turbo-gold text-turbo-navy' : 'border-turbo-blue/15 bg-white text-turbo-ink' }}">Vloeistoffen</a>
        <a href="{{ route('products.index') }}" class="whitespace-nowrap rounded-full border px-4 py-2 {{ request()->routeIs('products.index') ? 'border-turbo-gold bg-turbo-gold text-turbo-navy' : 'border-turbo-blue/15 bg-white text-turbo-ink' }}">Overige producten</a>
    </nav>
</div>

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
            action="{{ route($routeName) }}"
            x-data="{
                min: {{ request('min_price', 0) }},
                max: {{ request('max_price', 500) }}
            }"
            @change="$el.submit()"
            class="turbo-filters space-y-0"
        >

            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <!-- Sidebar header + reset -->
            <div class="flex items-center justify-between pb-4 mb-2 border-b border-[var(--turbo-border)]">
                <span class="font-bold text-turbo-ink text-sm">Filters</span>
                @if($activeFilterCount > 0)
                    <a
                        href="{{ route($routeName) }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-turbo-gold hover:text-turbo-gold-light transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset alles
                    </a>
                @endif
            </div>

            <!-- CATEGORIE -->
            <div class="py-4 border-b border-[var(--turbo-border)]">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Categorie</h3>
                <ul class="space-y-2.5 text-sm">
                    @foreach ($categories as $category)
                        <li>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="{{ $category->id }}"
                                    @checked(in_array((string) $category->id, request('categories', [])))
                                    class="form-checkbox h-4 w-4 rounded text-turbo-gold border-turbo-blue/30 focus:ring-turbo-gold/20"
                                >
                                <span class="group-hover:text-turbo-gold transition-colors">{{ $category->name }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if($brands->isNotEmpty())
                <!-- MERK -->
                <div class="py-4 border-b border-[var(--turbo-border)]">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Merk</h3>
                    <ul class="space-y-2.5 text-sm">
                        @foreach ($brands as $brand)
                            <li>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" name="brands[]" value="{{ $brand }}"
                                        @checked(in_array($brand, request('brands', [])))
                                        class="form-checkbox h-4 w-4 rounded text-turbo-gold border-turbo-blue/30 focus:ring-turbo-gold/20">
                                    <span class="group-hover:text-turbo-gold transition-colors">{{ $brand }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($modelTypes->isNotEmpty())
                <!-- BRANDERSTYPE -->
                <div class="py-4 border-b border-[var(--turbo-border)]">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Branderstype</h3>
                    <ul class="space-y-2.5 text-sm">
                        @foreach ($modelTypes as $mt)
                            <li>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox" name="model_types[]" value="{{ $mt }}"
                                        @checked(in_array($mt, request('model_types', [])))
                                        class="form-checkbox h-4 w-4 rounded text-turbo-gold border-turbo-blue/30 focus:ring-turbo-gold/20">
                                    <span class="group-hover:text-turbo-gold transition-colors capitalize">{{ $mt }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- STAAT -->
            <div class="py-4 border-b border-[var(--turbo-border)]">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Staat</h3>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="condition" value=""
                                @checked(!request('condition'))
                                class="form-radio h-4 w-4 text-turbo-gold border-turbo-blue/30 focus:ring-turbo-gold/20">
                            <span class="group-hover:text-turbo-gold transition-colors">Alle producten</span>
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="condition" value="new"
                                @checked(request('condition') === 'new')
                                class="form-radio h-4 w-4 text-turbo-gold border-turbo-blue/30 focus:ring-turbo-gold/20">
                            <span class="group-hover:text-turbo-gold transition-colors">Nieuw</span>
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="condition" value="used"
                                @checked(request('condition') === 'used')
                                class="form-radio h-4 w-4 text-turbo-gold border-turbo-blue/30 focus:ring-turbo-gold/20">
                            <span class="group-hover:text-turbo-gold transition-colors">Gebruikt</span>
                        </label>
                    </li>
                </ul>
            </div>

            <!-- PRIJS -->
            <div class="py-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Prijs</h3>

                <input type="range" min="0" max="500" step="5" x-model="min" class="w-full mb-2">
                <input type="range" min="0" max="500" step="5" x-model="max" class="w-full">

                <div class="text-sm text-gray-500 mt-3">
                    € <span x-text="min"></span> — € <span x-text="max"></span>
                </div>

                <input type="hidden" name="min_price" :value="min">
                <input type="hidden" name="max_price" :value="max">
            </div>

        </form>

    </aside>

    <!-- PRODUCT GRID -->
    <section class="lg:col-span-3">

        <!-- Mobile filter trigger -->
        <div class="lg:hidden grid grid-cols-2 gap-2 rounded-xl border border-turbo-blue/10 bg-white p-2 shadow-sm mb-4">
            <button
                type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-turbo-blue/15 bg-turbo-gray/60 px-3 py-2 text-sm font-semibold text-turbo-ink hover:border-turbo-gold transition-colors"
                @click="mobileFilters = true"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 6h18M6 12h12m-9 6h6" />
                </svg>
                Filters
                @if($activeFilterCount > 0)
                    <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-turbo-gold text-turbo-navy text-xs font-bold leading-none">{{ $activeFilterCount }}</span>
                @endif
            </button>
            <form method="GET" action="{{ route($routeName) }}">
                @foreach (request()->except('sort', 'page') as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label class="sr-only" for="mobile-product-sort">Sorteren</label>
                <select id="mobile-product-sort" name="sort" onchange="this.form.submit()" class="h-11 w-full rounded-lg border-turbo-blue/15 bg-white py-2 pl-3 pr-8 text-sm font-semibold text-turbo-ink focus:border-turbo-gold focus:ring-turbo-gold/20">
                    <option value="">Sorteren</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Prijs laag–hoog</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Prijs hoog–laag</option>
                    <option value="newest" @selected(request('sort') === 'newest')>Nieuwste</option>
                </select>
            </form>
        </div>
        @if($activeFilterCount > 0)
            <div class="-mt-2 mb-4 flex items-center justify-between rounded-lg bg-turbo-gold/10 px-3 py-2 text-xs font-semibold text-turbo-ink lg:hidden">
                <span>{{ $activeFilterCount }} {{ $activeFilterCount === 1 ? 'filter actief' : 'filters actief' }}</span>
                <a href="{{ route($routeName) }}" class="text-turbo-blue underline decoration-turbo-gold underline-offset-2">Filters wissen</a>
            </div>
        @endif

        <!-- TOP BAR -->
        <div class="hidden lg:flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">

            <div class="flex items-center gap-3">
                <p class="text-sm text-gray-500">
                    {{ $products->total() }} producten gevonden
                </p>
                @if($activeFilterCount > 0)
                    <a
                        href="{{ route($routeName) }}"
                        class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold text-turbo-gold hover:text-turbo-gold-light transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset alles
                    </a>
                @endif
            </div>

            <!-- SORTEREN -->
            <form method="GET" action="{{ route($routeName) }}">
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
                    class="form-select text-sm rounded-lg border-turbo-blue/20 bg-white shadow-sm text-turbo-ink font-medium cursor-pointer focus:border-turbo-gold focus:ring-turbo-gold/20 py-2"
                >
                    <option value="">Sorteren op</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Prijs: laag → hoog</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Prijs: hoog → laag</option>
                    <option value="newest" @selected(request('sort') === 'newest')>Nieuwste eerst</option>
                </select>
            </form>

        </div>

        <!-- PRODUCTEN -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse ($products as $product)
                @php
                    $productSummary = Str::limit(
                        preg_replace('/\s+/', ' ', strip_tags($product->description ?? '')),
                        90
                    );
                @endphp
                <article class="product-card group">

                    <a
                        href="{{ route('product.show', $product->slug) }}"
                        class="product-card__media product-card__media--white relative block h-32 sm:h-44 flex items-center justify-center"
                    >
                        @if($product->used)
                            <span class="absolute top-2 left-2 z-10 rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-700">Gebruikt</span>
                        @endif
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <span class="text-turbo-ink font-semibold text-center px-3">{{ $product->name }}</span>
                        @endif
                    </a>

                    <div class="p-3 sm:p-4 flex flex-1 flex-col">
                        <h3 class="text-sm sm:text-base font-semibold leading-snug mb-1">
                            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-turbo-gold">
                                {{ $product->name }}
                            </a>
                        </h3>

                        @if($productSummary)
                            <p class="mb-4 hidden text-sm leading-5 text-gray-500 sm:block">
                                {{ $productSummary }}
                            </p>
                        @endif

                        <div class="mt-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 pt-2">
                            <span class="product-card__price text-sm sm:text-base">
                                € {{ number_format($product->price, 2, ',', '.') }}
                            </span>

                            <form method="POST" action="{{ route('cart.add', $product->id) }}">
                                @csrf
                                <button class="turbo-button w-full sm:w-auto justify-center px-2 sm:px-3 py-2 text-xs">
                                    <span class="sm:hidden">Bestellen</span>
                                    <span class="hidden sm:inline">In winkelmand</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-2 lg:col-span-3 rounded-2xl border border-turbo-blue/10 bg-white px-6 py-10 text-center">
                    <h2 class="text-lg font-semibold text-turbo-ink">Geen producten gevonden</h2>
                    <p class="mt-2 text-sm text-gray-500">Pas de filters aan om meer producten te bekijken.</p>
                    <a href="{{ route($routeName) }}" class="turbo-button mt-5 inline-flex px-5 py-2.5 text-sm">Wis alle filters</a>
                </div>
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
        class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-2xl p-5 space-y-6 max-h-[80vh] overflow-y-auto border-t-2 border-turbo-gold"
        x-transition.origin-bottom
    >
        <!-- Drawer header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-gray-800">Filters</span>
                @if($activeFilterCount > 0)
                    <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-turbo-gold text-turbo-navy text-xs font-bold leading-none">{{ $activeFilterCount }}</span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if($activeFilterCount > 0)
                    <a href="{{ route($routeName) }}" class="text-xs font-semibold text-turbo-gold hover:text-turbo-gold-light transition-colors">
                        Reset alles
                    </a>
                @endif
                <button class="p-2 text-gray-500 hover:text-gray-700" @click="mobileFilters = false" aria-label="Sluit filters">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route($routeName) }}"
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

            <!-- Categorie pills -->
            <div class="space-y-3">
                <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Categorie</div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <label
                            x-data="{ checked: {{ in_array((string) $category->id, request('categories', [])) ? 'true' : 'false' }} }"
                            :class="checked
                                ? 'bg-turbo-gold text-turbo-navy border-turbo-gold font-semibold shadow-sm'
                                : 'bg-white text-gray-700 border-gray-200 hover:border-turbo-gold/60'"
                            class="inline-flex items-center rounded-full border px-4 py-2 cursor-pointer transition-all"
                        >
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                                x-model="checked"
                                class="sr-only"
                            >
                            <span class="text-sm">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            @if($brands->isNotEmpty())
                <!-- Merk pills -->
                <div class="space-y-3">
                    <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Merk</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($brands as $brand)
                            <label
                                x-data="{ checked: {{ in_array($brand, request('brands', [])) ? 'true' : 'false' }} }"
                                :class="checked ? 'bg-turbo-gold text-turbo-navy border-turbo-gold font-semibold shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover:border-turbo-gold/60'"
                                class="inline-flex items-center rounded-full border px-4 py-2 cursor-pointer transition-all"
                            >
                                <input type="checkbox" name="brands[]" value="{{ $brand }}" x-model="checked" class="sr-only">
                                <span class="text-sm">{{ $brand }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($modelTypes->isNotEmpty())
                <!-- Branderstype pills -->
                <div class="space-y-3">
                    <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Branderstype</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($modelTypes as $mt)
                            <label
                                x-data="{ checked: {{ in_array($mt, request('model_types', [])) ? 'true' : 'false' }} }"
                                :class="checked ? 'bg-turbo-gold text-turbo-navy border-turbo-gold font-semibold shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover:border-turbo-gold/60'"
                                class="inline-flex items-center rounded-full border px-4 py-2 cursor-pointer transition-all"
                            >
                                <input type="checkbox" name="model_types[]" value="{{ $mt }}" x-model="checked" class="sr-only">
                                <span class="text-sm capitalize">{{ $mt }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Staat -->
            <div class="space-y-3">
                <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Staat</div>
                <div class="flex flex-wrap gap-2">
                    @foreach (['' => 'Alle', 'new' => 'Nieuw', 'used' => 'Gebruikt'] as $val => $label)
                        <label
                            x-data="{ checked: {{ request('condition', '') === $val ? 'true' : 'false' }} }"
                            :class="checked ? 'bg-turbo-gold text-turbo-navy border-turbo-gold font-semibold shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover:border-turbo-gold/60'"
                            class="inline-flex items-center rounded-full border px-4 py-2 cursor-pointer transition-all"
                        >
                            <input type="radio" name="condition" value="{{ $val }}" x-model="checked" class="sr-only">
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Prijs -->
            <div class="space-y-3">
                <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Prijs</div>
                <div class="flex items-center gap-3">
                    <input type="range" min="0" max="500" step="5" x-model="min" class="w-full">
                    <input type="range" min="0" max="500" step="5" x-model="max" class="w-full">
                </div>
                <div class="text-sm text-gray-500">
                    € <span x-text="min"></span> — € <span x-text="max"></span>
                </div>
                <input type="hidden" name="min_price" :value="min">
                <input type="hidden" name="max_price" :value="max">
            </div>

        </form>
    </div>
</div>
</div>

@endsection
