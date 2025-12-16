@extends('themes.default.layouts.app')

@section('title', 'Alle producten')

@section('content')

<h1 class="text-3xl font-bold text-green-700 mb-8">
    Alle producten
</h1>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-10">

    <!-- FILTER SIDEBAR -->
    <aside class="lg:col-span-1">

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

        <!-- TOP BAR -->
        <div class="flex justify-between items-center mb-6">

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
                    class="border rounded p-2 text-sm"
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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

</div>

@endsection
