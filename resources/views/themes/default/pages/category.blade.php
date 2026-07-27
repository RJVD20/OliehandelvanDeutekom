@extends('themes.default.layouts.app')

@section('title', $category->name)

@section('content')

<!-- Titel -->
<section class="mb-6 sm:mb-10">
    <div class="rounded-2xl border border-turbo-blue/10 bg-white px-5 py-5 shadow-sm sm:px-7 sm:py-7">
        <p class="turbo-section-label mb-1.5 sm:mb-2">Categorie</p>
        <h1 class="text-2xl font-bold leading-tight sm:text-3xl">{{ $category->name }}</h1>
        <p class="mt-1.5 text-sm text-gray-500">{{ $products->total() }} producten in deze categorie</p>
    </div>

    <nav class="mt-3 flex gap-2 overflow-x-auto pb-1 text-sm font-semibold" aria-label="Productgroepen">
        <a href="{{ route('products.heaters') }}" class="whitespace-nowrap rounded-full border border-turbo-blue/15 bg-white px-4 py-2 text-turbo-ink">Kachels</a>
        <a href="{{ route('products.liquids') }}" class="whitespace-nowrap rounded-full border border-turbo-blue/15 bg-white px-4 py-2 text-turbo-ink">Vloeistoffen</a>
        <a href="{{ route('products.index') }}" class="whitespace-nowrap rounded-full border border-turbo-blue/15 bg-white px-4 py-2 text-turbo-ink">Overige producten</a>
    </nav>
</section>

<!-- Producten -->
<section>
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse ($products as $product)
            <article class="product-card">

                <a href="{{ url('/product/' . $product->slug) }}"
                   class="product-card__media product-card__media--white h-32 sm:h-40 flex items-center justify-center">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <span class="text-turbo-ink font-semibold text-center px-2">{{ $product->name }}</span>
                    @endif
                </a>

                <div class="flex flex-1 flex-col p-3 sm:p-4">
                    <h2 class="text-sm sm:text-base font-semibold leading-snug mb-1">{{ $product->name }}</h2>
                    <p class="hidden sm:block text-sm text-gray-500 mb-3 line-clamp-2">
                        {{ $product->description ?? 'Geen beschrijving beschikbaar' }}
                    </p>

                    <div class="mt-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-2">
                        <span class="product-card__price text-sm sm:text-base">
                            € {{ number_format($product->price, 2, ',', '.') }}
                        </span>

                        <form method="POST" action="{{ route('cart.add', $product->id) }}">
                            @csrf
                            <button class="turbo-button w-full justify-center px-2 sm:px-3 py-2 text-xs">
                                <span class="sm:hidden">Bestellen</span>
                                <span class="hidden sm:inline">In winkelmand</span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-2 lg:col-span-4 rounded-2xl border border-turbo-blue/10 bg-white px-6 py-10 text-center">
                <h2 class="text-lg font-semibold text-turbo-ink">Geen producten gevonden</h2>
                <p class="mt-2 text-sm text-gray-500">In deze categorie staan momenteel geen producten.</p>
                <a href="{{ route('products.index') }}" class="turbo-button mt-5 inline-flex px-5 py-2.5 text-sm">Bekijk het assortiment</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</section>

@endsection
