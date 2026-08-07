@extends('themes.default.layouts.app')

@section('title', 'Kachelvloeistof en petroleum kopen')
@section('description', 'Kachelvloeistof, petroleum, kachels en toebehoren bestellen. Profiteer van staffelprijzen, gratis bezorging vanaf 3 jerrycans en persoonlijk advies.')

@section('content')

@php
    use App\Models\Setting;
    use Illuminate\Support\Str;

    $cmsValue = function (string $key, string $default) {
        $value = Setting::get($key, null);
        if (is_string($value) && trim($value) === '') {
            return $default;
        }
        return $value ?? $default;
    };

    $products = $products ?? collect();
    $categories = $categories ?? collect();
    $heroImage = $heroImage ?? null;
    $heroImageSetting = Setting::get('home_hero_image', null);
    if (! $heroImage && is_string($heroImageSetting) && trim($heroImageSetting) !== '') {
        $heroImageSetting = trim($heroImageSetting);
        $heroImage = Str::startsWith($heroImageSetting, ['http://', 'https://', '/'])
            ? $heroImageSetting
            : asset('storage/' . $heroImageSetting);
    }
    $fallbackProductImage = isset($products) && $products->first()?->image ? asset('storage/' . $products->first()->image) : null;
    $turboBannerDesktop = asset('images/turbo-heating-banner-desktop-2400x800.webp');
    $turboBannerDesktopRetina = asset('images/turbo-heating-banner-desktop-4800x1600.webp');
    $turboBannerMobile = asset('images/turbo-heating-banner-mobile-1080x1200.webp');
    $turboBannerMobileRetina = asset('images/turbo-heating-banner-mobile-2160x2400.webp');

    $heroTitle = $cmsValue('home_hero_title', 'Bakker Brandstoffen in Den Helder');
    $heroSubtitle = $cmsValue('home_hero_subtitle', '');
    $heroIntro = $cmsValue('home_hero_intro', 'Al meer dan 75 jaar een begrip in Den Helder en omstreken. Voor advies en service op gebied van gasapparatuur zoals propaan gaskachels, kooktoestellen, campingartikelen, lasapparatuur, barbecues en terrasverwarming.');
    $heroCtaLabel = $cmsValue('home_hero_cta_label', 'Naar de webshop');
    $productSectionTitle = $cmsValue('home_products_title', 'Populaire producten');
    $productSectionIntro = $cmsValue('home_products_intro', 'Rustige, zorgvuldig gekozen items met focus op kwaliteit en betrouwbaarheid.');
    $tierPricesText = $cmsValue('home_tier_prices_text', 'Staffelprijzen bekijken? Open een productpagina voor alle prijzen per afnamehoeveelheid.');
    $productIntroParagraphs = array_values(array_filter(array_map(
        'trim',
        preg_split('/\R\s*\R/u', trim($productSectionIntro))
    )));
    $productIntroCards = [
        ['title' => 'Kwaliteit & prijs', 'icon' => 'M12 3 4 7v5c0 4.8 3.2 7.8 8 9 4.8-1.2 8-4.2 8-9V7l-8-4Zm-3 9 2 2 4-4'],
        ['title' => 'Nieuwe & gebruikte kachels', 'icon' => 'M12 3c2.2 2.8 4 4.8 4 7.5A4 4 0 0 1 8 16c0-2 1-3.7 2.6-5.7.1 1.9.8 3 1.4 3.7.7-1.6.6-3.5 0-6Z'],
        ['title' => 'Reparatie & onderhoud', 'icon' => 'm14.7 6.3 3-3 3 3-3 3M13 8l3 3-8.5 8.5H4v-3.5L13 8Z'],
    ];
    $categoriesTitle = $cmsValue('home_categories_title', 'Shop per categorie');
    $faqTitle = $cmsValue('home_faq_title', 'Veelgestelde vragen');

    $defaultFaqs = [
        [
            'q' => 'Wat zijn de bezorgmogelijkheden?',
            'a' => 'We stemmen de bezorging in overleg met je af. Zo weet je vooraf waar je aan toe bent en wanneer we kunnen leveren.',
        ],
        [
            'q' => 'Hoe snel wordt mijn bestelling geleverd?',
            'a' => 'De levertijd hangt af van je locatie en de routeplanning. Na je bestelling nemen we contact op om een passend moment te plannen.',
        ],
        [
            'q' => 'Welke betaalmethoden kan ik gebruiken?',
            'a' => 'Je kunt veilig afrekenen via de beschikbare betaalopties in de checkout. Indien van toepassing bespreken we dit ook bij levering.',
        ],
        [
            'q' => 'Kan ik ook afhalen?',
            'a' => 'Afhalen is mogelijk bij onze ophaallocaties. Bekijk de locatiespagina voor actuele informatie.',
        ],
        [
            'q' => 'Hoe werkt retourneren?',
            'a' => 'Niet tevreden? Neem contact met ons op, dan kijken we samen naar een passende oplossing volgens de geldende voorwaarden.',
        ],
    ];

    $faqs = collect($defaultFaqs)->map(function ($item, $index) use ($cmsValue) {
        $i = $index + 1;
        $q = $cmsValue("home_faq_{$i}_q", $item['q']);
        $a = $cmsValue("home_faq_{$i}_a", $item['a']);
        return ['q' => $q, 'a' => $a];
    });
@endphp

<section class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden -mt-6 mb-10 sm:mb-16 md:mb-24 bg-turbo-navy">
    <div class="relative aspect-[9/10] w-full md:aspect-[3/1]">
        <div class="absolute inset-0 bg-turbo-navy"></div>
        <picture>
            <source
                media="(min-width: 768px)"
                srcset="{{ $turboBannerDesktop }} 1x, {{ $turboBannerDesktopRetina }} 2x"
            >
            <img
                src="{{ $turboBannerMobile }}"
                srcset="{{ $turboBannerMobile }} 1x, {{ $turboBannerMobileRetina }} 2x"
                alt="Turbo Heating GTL kachelbrandstof"
                class="absolute inset-0 h-full w-full object-cover object-center"
                fetchpriority="high"
            >
        </picture>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-turbo-navy/20"></div>
        <div class="absolute right-[12%] top-0 h-full w-px rotate-[18deg] bg-turbo-gold/50"></div>
        <div class="absolute right-[16%] top-0 h-full w-px rotate-[18deg] bg-turbo-gold/20"></div>

        <svg class="absolute -bottom-1 left-0 w-full h-20 sm:h-24 md:h-28 text-turbo-navy" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
            <path fill="currentColor" d="M0,74 C120,92 210,62 320,72 C430,82 490,112 640,100 C780,90 860,58 980,70 C1100,82 1210,112 1440,96 L1440,120 L0,120 Z"></path>
            <path fill="rgba(255,255,255,0.10)" d="M0,86 C140,100 240,74 340,82 C470,94 530,112 680,106 C830,100 900,76 1040,86 C1160,94 1260,114 1440,106 L1440,120 L0,120 Z"></path>
        </svg>
    </div>

    <div class="bg-turbo-navy pt-8 sm:pt-14 md:pt-16 pb-10 sm:pb-16 md:pb-18 border-b border-turbo-gold/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-7 sm:gap-10 lg:gap-12 items-start">
                <div class="max-w-xl">
                    <p class="turbo-section-label mb-2 sm:mb-3">Kachels &amp; vloeistoffen</p>
                    <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold leading-tight !text-white">
                        {{ $heroTitle }}
                    </h1>
                    @if($heroSubtitle)
                        <p class="mt-3 sm:mt-4 max-w-xl text-[15px] sm:text-lg font-semibold leading-6 sm:leading-relaxed text-turbo-gold-light">
                            {{ $heroSubtitle }}
                        </p>
                    @endif
                    <p class="mt-3 sm:mt-4 text-[14px] sm:text-base text-white/75 leading-6 sm:leading-relaxed">
                        {{ $heroIntro }}
                    </p>

                    <div class="mt-5 sm:mt-6 flex items-center gap-6">
                        <a href="{{ route('products.index') }}" class="turbo-button w-full justify-center px-6 py-3 text-sm focus:outline-none sm:w-auto">
                            {{ $heroCtaLabel }}
                        </a>

                        <div class="hidden sm:block opacity-40">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" class="h-20 w-20 text-turbo-gold" fill="none" aria-hidden="true">
                                <path d="M64 8c21.54 0 39 17.46 39 39 0 14.9-8.44 27.83-20.77 34.27L88 120l-24-10-24 10 5.77-38.73C33.44 74.83 25 61.9 25 47 25 25.46 42.46 8 64 8Z" stroke="currentColor" stroke-width="4" opacity=".9" />
                                <path d="M64 28l6.8 13.77L86 44.02l-11 10.74L77.6 70 64 62.8 50.4 70 53 54.76 42 44.02l15.2-2.25L64 28Z" fill="currentColor" opacity=".25" />
                                <path d="M52 92h24" stroke="currentColor" stroke-width="4" stroke-linecap="round" opacity=".55" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="w-full">
                    <p class="mb-3 text-[11px] font-bold uppercase tracking-[0.18em] text-white/55 sm:hidden">Snel naar</p>
                    <div class="grid grid-cols-2 gap-2.5 sm:gap-5 max-w-xl lg:ml-auto">
                        @php
                            $heroCategories = isset($categories) ? $categories->take(4) : collect();
                            $fallbackHeroCards = collect([
                                ['label' => 'Barbecues', 'route' => route('products.index')],
                                ['label' => 'Verwarming', 'route' => route('products.index')],
                                ['label' => 'Kamperen', 'route' => route('products.index')],
                                ['label' => 'Gastechniek', 'route' => route('products.index')],
                            ]);
                        @endphp

                        @forelse($heroCategories as $category)
                            @php $iconIndex = $loop->index % 4; @endphp
                            <a href="{{ route('category.show', $category->slug) }}" class="group rounded-xl bg-turbo-dark border border-white/10 shadow-[0_16px_40px_-30px_rgba(0,0,0,0.75)] px-3 sm:px-6 py-4 sm:py-7 flex flex-col items-center justify-center text-center gap-2 sm:gap-3 hover:border-turbo-gold hover:bg-turbo-blue focus:outline-none">
                                <span class="text-turbo-gold-light">
                                    @if($iconIndex === 0)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 10h10" />
                                            <path d="M8 10a4 4 0 0 1 8 0" />
                                            <path d="M9 10v2a3 3 0 0 0 6 0v-2" />
                                            <path d="M6 14h12" />
                                            <path d="M8 14v6" />
                                            <path d="M16 14v6" />
                                            <path d="M5 20h14" />
                                        </svg>
                                    @elseif($iconIndex === 1)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 6c2 2 2 4 0 6" />
                                            <path d="M12 6c2 2 2 4 0 6" />
                                            <path d="M17 6c2 2 2 4 0 6" />
                                            <path d="M6 18h12" />
                                            <path d="M7 18v-3a5 5 0 0 1 10 0v3" />
                                        </svg>
                                    @elseif($iconIndex === 2)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 20 12 4l9 16" />
                                            <path d="M7 20 12 11l5 9" />
                                            <path d="M10 20v-4h4v4" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 4h4" />
                                            <path d="M9 4c-2 2-3 4-3 7v7a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-7c0-3-1-5-3-7" />
                                            <path d="M9 10h6" />
                                        </svg>
                                    @endif
                                </span>

                                <span class="text-sm sm:text-base font-semibold text-white leading-tight">{{ $category->name }}</span>
                            </a>
                        @empty
                            @foreach($fallbackHeroCards as $card)
                                @php $iconIndex = $loop->index % 4; @endphp
                                <a href="{{ $card['route'] }}" class="group rounded-xl bg-turbo-dark border border-white/10 shadow-[0_16px_40px_-30px_rgba(0,0,0,0.75)] px-3 sm:px-6 py-4 sm:py-7 flex flex-col items-center justify-center text-center gap-2 sm:gap-3 hover:border-turbo-gold hover:bg-turbo-blue focus:outline-none">
                                    <span class="text-turbo-gold-light">
                                        @if($iconIndex === 0)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 10h10" />
                                                <path d="M8 10a4 4 0 0 1 8 0" />
                                                <path d="M9 10v2a3 3 0 0 0 6 0v-2" />
                                                <path d="M6 14h12" />
                                                <path d="M8 14v6" />
                                                <path d="M16 14v6" />
                                                <path d="M5 20h14" />
                                            </svg>
                                        @elseif($iconIndex === 1)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 6c2 2 2 4 0 6" />
                                                <path d="M12 6c2 2 2 4 0 6" />
                                                <path d="M17 6c2 2 2 4 0 6" />
                                                <path d="M6 18h12" />
                                                <path d="M7 18v-3a5 5 0 0 1 10 0v3" />
                                            </svg>
                                        @elseif($iconIndex === 2)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 20 12 4l9 16" />
                                                <path d="M7 20 12 11l5 9" />
                                                <path d="M10 20v-4h4v4" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-7 w-7 sm:h-9 sm:w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10 4h4" />
                                                <path d="M9 4c-2 2-3 4-3 7v7a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-7c0-3-1-5-3-7" />
                                                <path d="M9 10h6" />
                                            </svg>
                                        @endif
                                    </span>

                                    <span class="text-sm sm:text-base font-semibold text-white leading-tight">{{ $card['label'] }}</span>
                                </a>
                            @endforeach
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(($promotions ?? collect())->isNotEmpty())
<section class="mb-12 space-y-6 sm:mb-16" aria-labelledby="acties-heading">
    <div><p class="turbo-section-label">Tijdelijk voordeel</p><h2 id="acties-heading" class="mt-1 text-2xl font-bold sm:text-3xl">Acties</h2></div>
    @foreach($promotions as $promotion)
        <article class="overflow-hidden rounded-3xl border border-turbo-gold/35 bg-turbo-navy text-white shadow-2xl">
            <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(22rem,.8fr)]">
                @if($promotion->imageUrl())<img src="{{ $promotion->imageUrl() }}" alt="{{ $promotion->image_alt ?: $promotion->title }}" class="aspect-square h-full w-full object-cover">@endif
                <div class="flex flex-col justify-center p-6 sm:p-9 lg:p-12">
                    <span class="w-fit rounded-full bg-turbo-gold px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-turbo-navy">Aanbieding</span>
                    <h3 class="mt-4 text-3xl font-bold !text-white">{{ $promotion->title }}</h3>
                    @if($promotion->short_description)<p class="mt-3 leading-7 text-white/75">{{ $promotion->short_description }}</p>@endif
                    <ul class="mt-5 space-y-2 text-sm text-white/85">@foreach($promotion->items as $item)<li class="flex gap-2"><span class="font-bold text-turbo-gold">✓</span>{{ $item->quantity }}× {{ $item->label ?: $item->product->name }} @if($item->role === 'free')<strong class="text-turbo-gold">gratis</strong>@endif</li>@endforeach @if($promotion->free_shipping)<li class="flex gap-2"><span class="font-bold text-turbo-gold">✓</span>Gratis standaardverzending</li>@endif</ul>
                    <div class="mt-7 flex flex-wrap items-center gap-5"><div><span class="block text-xs text-white/50 line-through">Normaal € {{ number_format($promotion->normalValue(),2,',','.') }}</span><strong class="text-4xl text-turbo-gold">€ {{ number_format($promotion->fixed_price,2,',','.') }}</strong></div><form method="POST" action="{{ route('cart.add-promotion',$promotion) }}">@csrf<button class="turbo-button px-6 py-3">Voeg bundel toe</button></form></div>
                </div>
            </div>
        </article>
    @endforeach
</section>
@endif

<!-- PRODUCT GRID -->
<section id="producten" class="mb-12 sm:mb-20 md:mb-24">
    <div class="mb-6 sm:mb-8 md:mb-10">
        <div class="flex items-end justify-between gap-4">
            <div>
            <p class="turbo-section-label">Uitgelichte selectie</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight sm:text-2xl md:text-3xl">{{ $productSectionTitle }}</h2>
                <div class="mt-4 h-1 w-14 rounded-full bg-turbo-gold"></div>
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-3 md:gap-4 lg:mt-7">
            @foreach($productIntroParagraphs as $index => $paragraph)
                @php $card = $productIntroCards[$index] ?? ['title' => 'Onze service', 'icon' => 'M5 12h14M12 5v14']; @endphp
                <article class="group rounded-2xl border border-turbo-blue/10 bg-white p-4 shadow-[0_18px_45px_-36px_rgba(3,24,43,0.6)] sm:p-5">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-turbo-gold/15 text-turbo-ink">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="{{ $card['icon'] }}"/>
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold text-turbo-ink sm:text-base">{{ $card['title'] }}</h3>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-gray-600">{{ $paragraph }}</p>
                </article>
            @endforeach
        </div>
    </div>

    <a href="{{ route('products.index') }}" class="mb-5 flex items-center gap-3 rounded-xl border border-turbo-gold/40 bg-turbo-gold/10 px-4 py-3 text-sm text-turbo-ink transition hover:border-turbo-gold hover:bg-turbo-gold/15 sm:mb-6">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-turbo-gold text-turbo-navy" aria-hidden="true">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 7h16M7 12h10M9 17h6"/>
            </svg>
        </span>
        <span class="font-semibold">{{ $tierPricesText }}</span>
        <span class="ml-auto shrink-0 font-bold text-turbo-blue" aria-hidden="true">→</span>
    </a>

    <div class="mb-2 flex justify-end sm:hidden">
        <span class="text-[11px] font-semibold text-turbo-blue/60">Veeg om te bekijken →</span>
    </div>

    <div class="turbo-mobile-product-strip -mx-4 grid grid-flow-col auto-cols-[82%] items-stretch snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4 sm:mx-0 sm:grid-flow-row sm:auto-cols-auto sm:grid-cols-2 sm:gap-6 sm:overflow-visible sm:px-0 sm:pb-0 lg:grid-cols-3 md:gap-8">
        @forelse ($products as $product)
            <article class="product-card min-w-0 snap-start rounded-2xl">
                <a href="{{ route('product.show', $product->slug) }}" class="block">
                    <div class="product-card__media product-card__media--white aspect-[4/3] relative">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-turbo-ink text-sm font-semibold px-6 text-center">
                                {{ $product->name }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-white/10"></div>
                    </div>
                </a>

                <div class="p-4 sm:p-6 space-y-3 sm:space-y-4 flex-1 flex flex-col">
                    <div class="space-y-2">
                        <h3 class="text-lg sm:text-xl font-semibold leading-snug">
                            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-turbo-gold">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <p class="text-sm md:text-base text-gray-600 line-clamp-3">{{ $product->description ?? 'Geen beschrijving beschikbaar' }}</p>
                    </div>

                    <div class="mt-auto flex items-center justify-between gap-3 pt-2">
                        <span class="product-card__price text-lg">€ {{ number_format($product->price, 2, ',', '.') }}</span>

                        <form method="POST" action="{{ route('cart.add', $product->id) }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="turbo-button px-3 sm:px-4 py-2 text-sm">
                                <span class="sm:hidden">Bestellen</span>
                                <span class="hidden sm:inline">In winkelmand</span>
                                <span aria-hidden="true" class="text-base">→</span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="text-gray-600 col-span-3 text-center">Geen producten gevonden.</p>
        @endforelse
    </div>
</section>



<!-- CATEGORIES -->
<section class="mb-8 sm:mb-12 md:mb-16">
    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-4 sm:mb-6 md:mb-8">{{ $categoriesTitle }}</h2>

    <div class="bg-turbo-gray rounded-2xl sm:rounded-3xl px-3 sm:px-6 md:px-8 py-4 sm:py-8 md:py-10 overflow-hidden border border-turbo-blue/10">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-6 md:gap-8">
            @forelse ($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="group relative block rounded-2xl bg-turbo-dark text-white border border-white/10 shadow-[0_18px_40px_-30px_rgba(0,0,0,0.6)] overflow-hidden transition transform hover:-translate-y-1 hover:border-turbo-gold hover:shadow-[0_20px_50px_-28px_rgba(0,0,0,0.55)]">
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/5 transition"></div>

                    <div class="relative flex flex-row sm:flex-col items-center justify-start sm:justify-center gap-3 px-4 sm:px-8 py-4 sm:py-8">
                        <span class="inline-flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-full border border-turbo-gold/50 bg-turbo-navy/30 text-turbo-gold-light group-hover:border-turbo-gold-light">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 sm:h-6 sm:w-6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3c-1.5 2.2-.4 3.8.6 5.1.8 1.1 1.4 2 .7 3.6-.6 1.5-2.1 2.3-3.3 2.9-1.3.6-2.1 1.5-2.1 3.1 0 1.6 1.2 3 4.1 3 2.8 0 4.5-1.6 4.5-4 0-1.7-.8-2.5-1.3-3.4-.7-1.2-.6-2.3.3-3.7.9-1.5 1.4-3.4-.5-6.6" />
                            </svg>
                        </span>

                        <div class="text-left sm:text-center space-y-0.5 sm:space-y-1">
                            <div class="text-base sm:text-xl font-semibold">{{ $category->name }}</div>
                            <p class="text-xs sm:text-sm text-white/80">Bekijk producten</p>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-gray-700 col-span-3 text-center">Geen categorieën gevonden.</p>
            @endforelse
        </div>
    </div>
</section>



<!-- FAQ -->
<section class="mt-10 sm:mt-16 md:mt-20">
    <div class="flex items-center justify-between mb-4 sm:mb-6 md:mb-8">
        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold">{{ $faqTitle }}</h2>
        <span class="hidden sm:inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
            Snelle antwoorden
        </span>
    </div>

    <div class="grid gap-3 sm:gap-4 md:grid-cols-2">
        @php
            $faqs = $faqs ?? collect();
        @endphp

        @foreach($faqs as $item)
            <details class="group bg-white border border-turbo-blue/15 rounded-2xl shadow-sm hover:border-turbo-gold/60 hover:shadow-md transition open:border-turbo-gold/60">
                <summary class="cursor-pointer select-none px-4 sm:px-5 py-3.5 sm:py-4 text-sm sm:text-base font-semibold text-turbo-ink flex items-center gap-3">
                    <span class="inline-flex h-6 w-6 sm:h-7 sm:w-7 shrink-0 items-center justify-center rounded-full bg-turbo-gray text-turbo-gold text-[10px] sm:text-xs font-bold">
                        Q
                    </span>
                    <span class="flex-1">{{ $item['q'] ?? '' }}</span>
                    <span class="text-turbo-gold text-xl transition-transform duration-200 group-open:rotate-45">+</span>
                </summary>
                <div class="px-4 sm:px-5 pb-4 sm:pb-5 text-gray-700 leading-relaxed">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-turbo-gold text-turbo-navy text-xs font-bold">
                            A
                        </span>
                        <p class="text-sm md:text-base">{{ $item['a'] ?? '' }}</p>
                    </div>
                </div>
            </details>
        @endforeach
    </div>
</section>

<script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn (array $item) => [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['a'],
            ],
        ])->values()->all(),
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>

@endsection
