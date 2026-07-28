@extends('themes.default.layouts.app')

@section('title', $product->name)
@section('description', Str::limit(strip_tags($product->short_description ?: $product->description ?: $product->name.' bestellen bij Kachelvloeistof.nl.'), 155))
@section('social_image', $product->image ? asset('storage/'.$product->image) : asset('images/apple-touch-icon.png'))
@section('og_type', 'product')

@section('meta')

    <!-- JSON-LD structured data: Product -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
            'description' => strip_tags($product->description ?? ''),
            'sku' => $product->id,
            'brand' => filled($product->brand)
                ? ['@type' => 'Brand', 'name' => $product->brand]
                : null,
            'offers' => [
                '@type' => 'Offer',
                'url' => url()->current(),
                'priceCurrency' => 'EUR',
                'price' => number_format($product->price, 2, '.', ''),
                'availability' => $product->active ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $product->category->name ?? 'Producten', 'item' => $product->category ? route('category.show', $product->category->slug) : route('products.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $product->name, 'item' => url()->current()],
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
        : array_slice($contentParagraphs, 0, 1);

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

<div class="mx-auto max-w-6xl px-1 pb-20 sm:px-0 sm:pb-0">
    <section class="grid gap-5 lg:grid-cols-[minmax(0,1.08fr)_minmax(24rem,0.92fr)] lg:items-start" aria-labelledby="product-title">
        <div class="turbo-card overflow-hidden bg-white">
            @if($product->image)
                <div class="flex min-h-72 items-center justify-center p-2 sm:min-h-96 sm:p-3 lg:min-h-[32rem]">
                    <img
                        src="{{ asset('storage/' . $product->image) }}"
                        alt="{{ $product->name }}"
                        class="max-h-[32rem] w-full object-contain"
                    >
                </div>
            @else
                <div class="flex min-h-72 items-center justify-center bg-turbo-gray px-8 sm:min-h-96 lg:min-h-[32rem]">
                    <div class="text-center">
                        <div class="text-lg font-semibold text-turbo-ink">{{ $product->name }}</div>
                        <div class="mt-1 text-sm text-turbo-blue">Geen afbeelding beschikbaar</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="turbo-card p-5 sm:p-6 lg:p-7">
            @if($product->category)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-turbo-gray text-turbo-ink border border-turbo-gold/35">
                    {{ $product->category->name }}
                </span>
            @endif

            <h1 id="product-title" class="mt-3 text-2xl font-bold tracking-tight text-turbo-ink sm:text-3xl lg:text-4xl">
                {{ $product->name }}
            </h1>

            @if(count($summaryParagraphs))
                <div class="mt-3">
                    <p class="line-clamp-3 text-[15px] leading-6 text-gray-600">
                        {{ trim(implode(' ', $summaryParagraphs)) }}
                    </p>
                    @if(count($contentParagraphs))
                        <a href="#productinformatie" class="mt-1.5 inline-flex text-sm font-bold text-turbo-blue hover:text-turbo-gold">
                            Lees meer
                        </a>
                    @endif
                </div>
            @endif

            @if($specifications->isNotEmpty())
                <div class="mt-4 grid gap-2 sm:grid-cols-2" aria-label="Belangrijkste kenmerken">
                    @foreach($specifications->take(4) as [$label, $value])
                        <div class="rounded-lg border border-turbo-blue/10 bg-turbo-gray/50 px-3 py-2">
                            <span class="block text-[11px] font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</span>
                            <strong class="mt-0.5 block text-sm text-turbo-ink">{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                @include('themes.default.components.delivery-notice', ['attributes' => new \Illuminate\View\ComponentAttributeBag()])
            </div>

            <div class="mt-4 flex items-center justify-between gap-4 border-t border-turbo-blue/10 pt-4">
                <div>
                    <span class="product-card__price text-2xl sm:text-3xl">
                        € {{ number_format($product->price, 2, ',', '.') }}
                    </span>
                    <span class="mt-0.5 block text-xs font-semibold {{ $product->active ? 'text-emerald-700' : 'text-red-600' }}">
                        {{ $product->active ? 'Beschikbaar' : 'Niet beschikbaar' }}
                    </span>
                </div>

                @if($product->active)
                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="turbo-button px-6 py-3">In winkelmand</button>
                    </form>
                @endif
            </div>

            @if($productRule && (!empty($productRule['delivery']) || !empty($productRule['pickup'])))
                <div class="mt-4 rounded-xl border border-turbo-gold/35 bg-turbo-gray/60 p-3">
                    <div class="flex flex-wrap items-baseline justify-between gap-1">
                        <h2 class="text-sm font-bold text-turbo-ink">Staffelprijzen</h2>
                        <p class="text-[11px] text-turbo-blue">Automatisch toegepast in je winkelmand</p>
                    </div>

                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                        @foreach(['delivery' => 'Thuisbezorgen', 'pickup' => 'Afhalen'] as $method => $label)
                            @continue(empty($productRule[$method]))
                            <div class="overflow-hidden rounded-lg border border-turbo-blue/15 bg-white">
                                <h3 class="border-b border-turbo-gold/25 bg-turbo-gold/10 px-2.5 py-1.5 text-[11px] font-bold text-turbo-ink">{{ $label }}</h3>
                                <div class="divide-y divide-gray-100 px-2.5">
                                    @foreach($productRule[$method] as $tier)
                                        <div class="flex items-center justify-between gap-2 py-1.5 text-xs">
                                            <span class="text-gray-600">{{ $tier['quantity'] }}+ stuks</span>
                                            <strong class="whitespace-nowrap text-turbo-ink">€ {{ number_format((float) $tier['price'], 2, ',', '.') }} p/st.</strong>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if(count($contentParagraphs) || $specifications->isNotEmpty())
        <section class="mt-8 sm:mt-12 grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(20rem,0.65fr)] lg:items-start">
            @if(count($contentParagraphs))
                <article id="productinformatie" class="turbo-card scroll-mt-28 p-5 sm:p-7 md:p-8">
                    <p class="turbo-section-label mb-2">Productinformatie</p>
                    <h2 class="text-xl font-semibold sm:text-2xl">Meer over {{ $product->name }}</h2>

                    <div class="mt-5 space-y-5 text-[15px] leading-7 text-gray-700 sm:text-base">
                        @foreach($contentParagraphs as $index => $paragraph)
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
                                    class="transition hover:text-turbo-gold"
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
        @if($product->active)
            <form method="POST" action="{{ route('cart.add', $product->id) }}" class="shrink-0">
                @csrf
                <button type="submit" class="turbo-button px-5 py-3 text-sm">In winkelmand</button>
            </form>
        @else
            <span class="shrink-0 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-500">Niet beschikbaar</span>
        @endif
    </div>
</div>

@endsection
