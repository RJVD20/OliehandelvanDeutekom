@extends('themes.default.layouts.app')

@section('title', 'Home')

@section('content')

<!-- HERO -->
<section class="mb-16 text-center">
    <h1 class="text-4xl font-bold text-green-700 mb-4">
        Duurzaam. Simpel. Groen.
    </h1>
    <p class="text-gray-600 max-w-2xl mx-auto">
        Ontdek onze zorgvuldig geselecteerde producten met focus op kwaliteit en duurzaamheid.
    </p>
</section>

<!-- PRODUCT GRID -->
<section class="mb-20">
    <h2 class="text-2xl font-semibold text-green-700 mb-6">
        Populaire producten
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition group">

                <!-- Klikbare image / header -->
                <a href="{{ route('product.show', $product->slug) }}"
                   class="block h-40 bg-green-50 flex items-center justify-center rounded-t-lg
                          group-hover:bg-green-100 transition">
                    <span class="text-green-700 font-semibold text-center px-3">
                        {{ $product->name }}
                    </span>
                </a>

                <div class="p-4">

                    <!-- Productnaam (extra klikbaar & SEO-friendly) -->
                    <h3 class="font-semibold text-gray-900 mb-1">
                        <a href="{{ route('product.show', $product->slug) }}"
                           class="hover:text-green-700 transition">
                            {{ $product->name }}
                        </a>
                    </h3>

                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                        {{ $product->description ?? 'Geen beschrijving beschikbaar' }}
                    </p>

                    <div class="flex items-center justify-between">
                        <span class="font-bold text-green-700">
                            € {{ number_format($product->price, 2, ',', '.') }}
                        </span>
<form method="POST" action="{{ route('cart.add', $product->id) }}">
    @csrf
    <button
        type="submit"
        class="px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition"
    >
        In winkelmand
    </button>
</form>


                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-4 text-center">
                Geen producten gevonden.
            </p>
        @endforelse
    </div>
</section>



<!-- CATEGORIES -->
<section class="mb-10">
    <h2 class="text-2xl font-semibold text-green-700 mb-6">
        Shop per categorie
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
        @forelse ($categories as $category)
          <a href="{{ route('category.show', $category->slug) }}"
            class="group bg-green-50 border border-green-100 rounded-lg p-6 text-center hover:bg-green-100 transition cursor-pointer">
              <div class="text-green-700 text-lg font-semibold mb-2">
                  {{ $category->name }}
              </div>
              <p class="text-sm text-green-600">
                  Bekijk producten
              </p>
          </a>
        @empty
            <p class="text-gray-500 col-span-4 text-center">
                Geen categorieën gevonden.
            </p>
        @endforelse
    </div>
</section>



@endsection
