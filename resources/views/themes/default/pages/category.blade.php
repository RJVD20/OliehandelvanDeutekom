@extends('themes.default.layouts.app')

@section('title', $category->name)

@section('content')

<!-- Titel -->
<section class="mb-10">
    <h1 class="text-3xl font-bold text-green-700 mb-2">
        {{ $category->name }}
    </h1>
    <p class="text-gray-600">
        Producten in deze categorie
    </p>
</section>

<!-- Producten -->
<section>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition">

                <a href="{{ url('/product/' . $product->slug) }}"
                   class="h-40 bg-green-50 flex items-center justify-center rounded-t-lg hover:bg-green-100 transition">
                    <span class="text-green-700 font-semibold text-center px-2">
                        {{ $product->name }}
                    </span>
                </a>

                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-3">
                        {{ $product->description ?? 'Geen beschrijving beschikbaar' }}
                    </p>

                    <div class="flex items-center justify-between">
                        <span class="font-bold text-green-700">
                            € {{ number_format($product->price, 2, ',', '.') }}
                        </span>

                        <button class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                            In winkelmand
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-4 text-center">
                Geen producten gevonden in deze categorie.
            </p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</section>

@endsection
