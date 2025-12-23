@extends('themes.default.layouts.app')

@section('title', $product->name)

@section('meta')
    <meta name="description" content="{{ Str::limit(strip_tags($product->description ?? ''), 160) }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($product->description ?? ''), 200) }}">
    <meta property="og:type" content="product">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($product->image)
        <meta property="og:image" content="{{ asset('storage/' . $product->image) }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product->name }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($product->description ?? ''), 200) }}">

    <!-- JSON-LD structured data: Product -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
            'description' => strip_tags($product->description ?? ''),
            'sku' => $product->id,
            'brand' => ['@type' => 'Brand', 'name' => $product->category->name ?? ''],
            'offers' => [
                '@type' => 'Offer',
                'url' => url()->current(),
                'priceCurrency' => 'EUR',
                'price' => number_format($product->price, 2, '.', ''),
                'availability' => $product->active ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

    <!-- Afbeelding -->
    <div class="bg-green-50 rounded-lg h-96 flex items-center justify-center">
        <span class="text-green-700 text-xl font-semibold text-center px-4">
            @if($product->image)
    <img
        src="{{ asset('storage/' . $product->image) }}"
        class="w-full h-64 object-cover rounded mb-4"
    >
@endif
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
