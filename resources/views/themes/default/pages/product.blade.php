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

<div class="max-w-6xl mx-auto px-1 sm:px-0">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 lg:gap-12">

        <!-- Afbeelding -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            @if($product->image)
                <div class="h-64 sm:h-72 md:h-80 bg-white flex items-center justify-center p-4 sm:p-6">
                    <img
                        src="{{ asset('storage/' . $product->image) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-contain"
                        loading="lazy"
                    >
                </div>
            @else
                <div class="h-64 sm:h-72 md:h-80 bg-green-50 flex items-center justify-center px-8">
                    <div class="text-center">
                        <div class="text-green-700 text-lg font-semibold">
                            {{ $product->name }}
                        </div>
                        <div class="text-sm text-green-600 mt-1">
                            Geen afbeelding beschikbaar
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Info -->
        <div class="bg-white border rounded-2xl shadow-sm p-5 md:p-8 space-y-4">
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-50 text-green-700 border border-green-100">
                    {{ $product->category->name }}
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 mb-4">
                {{ $product->name }}
            </h1>

            <div class="text-gray-600 leading-relaxed text-base md:text-lg">
                {{ $product->description ?? 'Geen beschrijving beschikbaar.' }}
            </div>

            <div class="mt-6 pt-4 border-t flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-2xl md:text-3xl font-bold text-green-700">
                    € {{ number_format($product->price, 2, ',', '.') }}
                </div>

                <form method="POST" action="{{ route('cart.add', $product->id) }}" class="w-full sm:w-auto">
                    @csrf
                    <button
                        type="submit"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                    >
                        In winkelmand
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(isset($suggestedProducts) && $suggestedProducts->count())
        <section class="mt-12">
            <header class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">
                    Voorgestelde producten
                </h2>
                <p class="text-gray-600">
                    Andere producten die bij deze categorie passen.
                </p>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
                @foreach($suggestedProducts as $suggested)
                    <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition group overflow-hidden">
                        <a
                            href="{{ route('product.show', $suggested->slug) }}"
                            class="block h-40 bg-green-50 group-hover:bg-green-100 transition"
                        >
                            @if($suggested->image)
                                <img
                                    src="{{ asset('storage/' . $suggested->image) }}"
                                    alt="{{ $suggested->name }}"
                                    class="h-40 w-full object-cover"
                                    loading="lazy"
                                >
                            @else
                                <div class="h-40 flex items-center justify-center px-3">
                                    <span class="text-green-700 font-semibold text-center">
                                        {{ $suggested->name }}
                                    </span>
                                </div>
                            @endif
                        </a>

                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1">
                                <a
                                    href="{{ route('product.show', $suggested->slug) }}"
                                    class="hover:text-green-700 transition"
                                >
                                    {{ $suggested->name }}
                                </a>
                            </h3>

                            <div class="flex items-center justify-between">
                                <span class="font-bold text-green-700">
                                    € {{ number_format($suggested->price, 2, ',', '.') }}
                                </span>

                                <form method="POST" action="{{ route('cart.add', $suggested->id) }}">
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
                @endforeach
            </div>
        </section>
    @endif
</div>

@endsection
