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

@php
    $descriptionSource = preg_replace(
        ['/<br\s*\/?>/i', '/<\/(?:p|div|h[1-6]|li)>/i'],
        ["\n", "$0\n\n"],
        $product->description ?? ''
    );
    $plainDescription = trim(strip_tags($descriptionSource));
    $descriptionParagraphs = $plainDescription !== ''
        ? array_values(array_filter(
            preg_split('/\R\s*\R/u', $plainDescription),
            fn ($paragraph) => trim($paragraph) !== ''
        ))
        : [];

    $specificationIndex = collect($descriptionParagraphs)
        ->search(fn ($paragraph) => mb_strtolower(trim($paragraph)) === 'specificaties');

    $contentParagraphs = $specificationIndex === false
        ? $descriptionParagraphs
        : array_slice($descriptionParagraphs, 0, $specificationIndex);

    $specificationLines = $specificationIndex === false
        ? []
        : array_slice($descriptionParagraphs, $specificationIndex + 1);

    $hasStructuredSummary = filled($product->short_description);
    $summaryParagraphs = $hasStructuredSummary
        ? array_values(array_filter(preg_split('/\R\s*\R/u', trim($product->short_description))))
        : (count($contentParagraphs) > 2 ? array_slice($contentParagraphs, 0, 2) : $contentParagraphs);

    $detailParagraphs = $hasStructuredSummary
        ? $contentParagraphs
        : (count($contentParagraphs) > 2 ? array_slice($contentParagraphs, 2) : []);

    $specifications = collect($product->specifications ?: [])
        ->map(fn (array $specification) => [
            trim((string) ($specification['name'] ?? '')),
            trim((string) ($specification['value'] ?? '')),
        ])
        ->filter(fn (array $specification) => $specification[0] !== '' && $specification[1] !== '')
        ->values();

    if ($specifications->isEmpty()) {
        $specifications = collect($specificationLines)
            ->map(function ($line) {
                if (! str_contains($line, ':')) {
                    return null;
                }

                [$label, $value] = array_map('trim', explode(':', $line, 2));

                return $label !== '' && $value !== '' ? [$label, $value] : null;
            })
            ->filter()
            ->values();
    }
@endphp

<div class="max-w-6xl mx-auto px-1 pb-20 sm:px-0 sm:pb-0">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 lg:gap-12">

        <!-- Afbeelding -->
        <div class="turbo-card overflow-hidden">
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
                <div class="h-64 sm:h-72 md:h-80 bg-turbo-gray flex items-center justify-center px-8">
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
        <div class="turbo-card p-5 md:p-8 space-y-4">
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-turbo-gray text-turbo-ink border border-turbo-gold/35">
                    {{ $product->category->name }}
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-4">
                {{ $product->name }}
            </h1>

            <div class="space-y-3 text-gray-600 leading-7 text-[15px] md:text-lg md:leading-relaxed">
                @forelse($summaryParagraphs as $paragraph)
                    <p>{{ trim($paragraph) }}</p>
                @empty
                    <p>Geen beschrijving beschikbaar.</p>
                @endforelse
            </div>

            @include('themes.default.components.delivery-notice', ['attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'pt-2'])])

            @if($productRule && (!empty($productRule['delivery']) || !empty($productRule['pickup'])))
                <div class="rounded-xl border border-turbo-gold/35 bg-turbo-gray/70 p-4">
                    <div class="mb-3">
                        <h2 class="text-sm font-bold text-turbo-ink">Voordeel bij meerdere stuks</h2>
                        <p class="mt-0.5 text-xs text-turbo-blue">De juiste stukprijs wordt automatisch in je winkelmand toegepast.</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach(['delivery' => 'Thuisbezorgen', 'pickup' => 'Afhalen'] as $method => $label)
                            @continue(empty($productRule[$method]))
                            <div class="overflow-hidden rounded-lg border border-turbo-blue/15 bg-white">
                                <h3 class="border-b border-turbo-gold/35 bg-turbo-gold/15 px-3 py-2 text-xs font-bold text-turbo-ink">{{ $label }}</h3>
                                <div class="divide-y divide-gray-100">
                                    @foreach($productRule[$method] as $tier)
                                        <div class="flex items-center justify-between gap-3 px-3 py-2 text-xs">
                                            <span class="text-gray-600">Vanaf {{ $tier['quantity'] }} stuks</span>
                                            <strong class="whitespace-nowrap text-turbo-ink">€ {{ number_format((float) $tier['price'], 2, ',', '.') }} p/st.</strong>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6 pt-4 border-t flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="product-card__price text-2xl md:text-3xl">
                    € {{ number_format($product->price, 2, ',', '.') }}
                </div>

                <form method="POST" action="{{ route('cart.add', $product->id) }}" class="hidden w-full sm:block sm:w-auto">
                    @csrf
                    <button
                        type="submit"
                        class="turbo-button w-full sm:w-auto px-6 py-3"
                    >
                        In winkelmand
                    </button>
                </form>
            </div>
        </div>
    </div>

    <section class="mt-8 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4" aria-label="Productvoordelen">
        @foreach([
            ['Geurloos', 'M4 12h16M12 4v16'],
            ['Veilig en schoon', 'M12 3 5 6v5c0 4.6 2.9 8 7 10 4.1-2 7-5.4 7-10V6l-7-3Z'],
            ['Vrij te transporteren', 'M3 7h11v10H3zM14 10h3l4 4v3h-7zM7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z'],
            ['Breed toepasbaar', 'M4 5h16v14H4zM8 9h8M8 13h5'],
        ] as [$benefit, $path])
            <div class="turbo-card flex items-center gap-3 p-3 sm:p-4">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-turbo-navy text-turbo-gold-light">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="{{ $path }}" />
                    </svg>
                </span>
                <span class="text-xs sm:text-sm font-semibold text-turbo-ink">{{ $benefit }}</span>
            </div>
        @endforeach
    </section>

    @if(count($detailParagraphs) || $specifications->isNotEmpty())
        <section class="mt-8 sm:mt-12 grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(20rem,0.65fr)] lg:items-start">
            @if(count($detailParagraphs))
                <article class="turbo-card p-5 sm:p-7 md:p-8">
                    <p class="turbo-section-label mb-2">Productinformatie</p>
                    <h2 class="text-xl font-semibold sm:text-2xl">Meer over {{ $product->name }}</h2>

                    <div class="mt-5 space-y-5 text-[15px] leading-7 text-gray-700 sm:text-base">
                        @foreach($detailParagraphs as $index => $paragraph)
                            @php
                                $paragraph = trim($paragraph);
                                $looksLikeHeading = mb_strlen($paragraph) <= 100
                                    && ! str_ends_with($paragraph, '.');
                            @endphp

                            @if($looksLikeHeading)
                                <h3 class="!mt-7 text-lg font-semibold leading-snug text-turbo-ink first:!mt-0">
                                    {{ $paragraph }}
                                </h3>
                            @else
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                </article>
            @endif

            @if($specifications->isNotEmpty())
                <aside class="turbo-card overflow-hidden lg:sticky lg:top-6">
                    <div class="border-b border-turbo-blue/10 bg-turbo-gray/60 px-5 py-4">
                        <p class="turbo-section-label mb-1">Details</p>
                        <h2 class="text-xl font-semibold">Specificaties</h2>
                    </div>
                    <dl class="divide-y divide-turbo-blue/10 px-5">
                        @foreach($specifications as [$label, $value])
                            <div class="grid grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)] gap-3 py-3 text-sm">
                                <dt class="font-semibold text-turbo-ink">{{ $label }}</dt>
                                <dd class="text-right leading-5 text-gray-600">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </aside>
            @endif
        </section>
    @endif

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
                    <article class="product-card group">
                        <a
                            href="{{ route('product.show', $suggested->slug) }}"
                            class="product-card__media product-card__media--white block h-40"
                        >
                            @if($suggested->image)
                                <img
                                    src="{{ asset('storage/' . $suggested->image) }}"
                                    alt="{{ $suggested->name }}"
                                    class="h-40 w-full object-contain"
                                    loading="lazy"
                                >
                            @else
                                <div class="h-40 flex items-center justify-center px-3">
                                    <span class="text-turbo-ink font-semibold text-center">
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
                                <span class="product-card__price">
                                    € {{ number_format($suggested->price, 2, ',', '.') }}
                                </span>

                                <form method="POST" action="{{ route('cart.add', $suggested->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="turbo-button px-3 py-2 text-xs"
                                    >
                                        In winkelmand
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>

<div class="fixed inset-x-0 bottom-0 z-[11000] border-t border-turbo-gold/35 bg-white/95 px-4 py-3 shadow-[0_-12px_35px_-20px_rgba(3,24,43,0.65)] backdrop-blur sm:hidden">
    <div class="mx-auto flex max-w-lg items-center gap-3">
        <div class="min-w-0 flex-1">
            <p class="truncate text-xs font-semibold text-gray-500">{{ $product->name }}</p>
            <p class="product-card__price text-lg">€ {{ number_format($product->price, 2, ',', '.') }}</p>
        </div>
        <form method="POST" action="{{ route('cart.add', $product->id) }}" class="shrink-0">
            @csrf
            <button type="submit" class="turbo-button px-5 py-3 text-sm">In winkelmand</button>
        </form>
    </div>
</div>

@endsection
