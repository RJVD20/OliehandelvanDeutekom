@extends('themes.default.layouts.app')

@section('title', $product->name)

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

    <!-- Afbeelding -->
    <div class="bg-green-50 rounded-lg h-96 flex items-center justify-center">
        <span class="text-green-700 text-xl font-semibold text-center px-4">
            {{ $product->name }}
        </span>
    </div>

    <!-- Info -->
    <div>
        <p class="text-sm text-green-600 mb-2">
            Categorie: {{ $product->category->name }}
        </p>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            {{ $product->name }}
        </h1>

        <p class="text-gray-600 mb-6">
            {{ $product->description ?? 'Geen beschrijving beschikbaar.' }}
        </p>

        <div class="flex items-center justify-between">
            <span class="text-2xl font-bold text-green-700">
                € {{ number_format($product->price, 2, ',', '.') }}
            </span>

            <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                In winkelmand
            </button>
        </div>
    </div>
</div>

@endsection
