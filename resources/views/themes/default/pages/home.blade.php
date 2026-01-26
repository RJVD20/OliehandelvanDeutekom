@extends('themes.default.layouts.app')

@section('title', 'Home')

@section('content')

@php
    use App\Models\Setting;

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
    $fallbackProductImage = isset($products) && $products->first()?->image ? asset('storage/' . $products->first()->image) : null;

    $heroTitle = $cmsValue('home_hero_title', 'Bakker Brandstoffen in Den Helder');
    $heroIntro = $cmsValue('home_hero_intro', 'Al meer dan 75 jaar een begrip in Den Helder en omstreken. Voor advies en service op gebied van gasapparatuur zoals propaan gaskachels, kooktoestellen, campingartikelen, lasapparatuur, barbecues en terrasverwarming.');
    $heroCtaLabel = $cmsValue('home_hero_cta_label', 'Naar de webshop');
    $productSectionTitle = $cmsValue('home_products_title', 'Populaire producten');
    $productSectionIntro = $cmsValue('home_products_intro', 'Rustige, zorgvuldig gekozen items met focus op kwaliteit en betrouwbaarheid.');
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

<section class="relative overflow-hidden -mx-4 sm:-mx-6 lg:-mx-8 -mt-6 mb-16 md:mb-24">
    <div class="relative h-[360px] sm:h-[440px] md:h-[520px]">
        <div class="absolute inset-0 bg-[#c7d1c2]"></div>
        @if($heroImage)
            <img src="{{ $heroImage }}" alt="Hero" class="absolute inset-0 h-full w-full object-cover">
        @elseif($fallbackProductImage)
            <img src="{{ $fallbackProductImage }}" alt="Hero" class="absolute inset-0 h-full w-full object-cover">
        @endif
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/10 to-transparent"></div>

        <svg class="absolute -bottom-1 left-0 w-full h-20 sm:h-24 md:h-28 text-[#a8ad98]" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
            <path fill="currentColor" d="M0,74 C120,92 210,62 320,72 C430,82 490,112 640,100 C780,90 860,58 980,70 C1100,82 1210,112 1440,96 L1440,120 L0,120 Z"></path>
            <path fill="rgba(255,255,255,0.10)" d="M0,86 C140,100 240,74 340,82 C470,94 530,112 680,106 C830,100 900,76 1040,86 C1160,94 1260,114 1440,106 L1440,120 L0,120 Z"></path>
        </svg>
    </div>

    <div class="bg-[#a8ad98] pt-12 sm:pt-14 md:pt-16 pb-14 sm:pb-16 md:pb-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-12 items-start">
                <div class="max-w-xl">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-semibold leading-tight text-[#2f2f2f]">
                        {{ $heroTitle }}
                    </h1>
                    <p class="mt-4 text-sm sm:text-base text-[#2f2f2f]/80 leading-relaxed">
                        {{ $heroIntro }}
                    </p>

                    <div class="mt-6 flex items-center gap-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-md bg-[#d85c3f] px-6 py-3 text-sm font-semibold text-white shadow-[0_14px_30px_-18px_rgba(0,0,0,0.65)] hover:bg-[#c95239] focus:outline-none focus:ring-2 focus:ring-white/70 focus:ring-offset-2 focus:ring-offset-[#a8ad98]">
                            {{ $heroCtaLabel }}
                        </a>

                        <div class="hidden sm:block opacity-40">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" class="h-20 w-20 text-[#2f2f2f]" fill="none" aria-hidden="true">
                                <path d="M64 8c21.54 0 39 17.46 39 39 0 14.9-8.44 27.83-20.77 34.27L88 120l-24-10-24 10 5.77-38.73C33.44 74.83 25 61.9 25 47 25 25.46 42.46 8 64 8Z" stroke="currentColor" stroke-width="4" opacity=".9" />
                                <path d="M64 28l6.8 13.77L86 44.02l-11 10.74L77.6 70 64 62.8 50.4 70 53 54.76 42 44.02l15.2-2.25L64 28Z" fill="currentColor" opacity=".25" />
                                <path d="M52 92h24" stroke="currentColor" stroke-width="4" stroke-linecap="round" opacity=".55" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="w-full">
                    <div class="grid grid-cols-2 gap-4 sm:gap-5 max-w-xl lg:ml-auto">
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
                            <a href="{{ route('category.show', $category->slug) }}" class="group rounded-xl bg-[#2f2f2f] border border-white/10 shadow-[0_16px_40px_-30px_rgba(0,0,0,0.75)] px-4 sm:px-6 py-6 sm:py-7 flex flex-col items-center justify-center text-center gap-3 hover:bg-[#3a3a3a] focus:outline-none focus:ring-2 focus:ring-white/70 focus:ring-offset-2 focus:ring-offset-[#a8ad98]">
                                <span class="text-white/80">
                                    @if($iconIndex === 0)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 10h10" />
                                            <path d="M8 10a4 4 0 0 1 8 0" />
                                            <path d="M9 10v2a3 3 0 0 0 6 0v-2" />
                                            <path d="M6 14h12" />
                                            <path d="M8 14v6" />
                                            <path d="M16 14v6" />
                                            <path d="M5 20h14" />
                                        </svg>
                                    @elseif($iconIndex === 1)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 6c2 2 2 4 0 6" />
                                            <path d="M12 6c2 2 2 4 0 6" />
                                            <path d="M17 6c2 2 2 4 0 6" />
                                            <path d="M6 18h12" />
                                            <path d="M7 18v-3a5 5 0 0 1 10 0v3" />
                                        </svg>
                                    @elseif($iconIndex === 2)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 20 12 4l9 16" />
                                            <path d="M7 20 12 11l5 9" />
                                            <path d="M10 20v-4h4v4" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
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
                                <a href="{{ $card['route'] }}" class="group rounded-xl bg-[#2f2f2f] border border-white/10 shadow-[0_16px_40px_-30px_rgba(0,0,0,0.75)] px-4 sm:px-6 py-6 sm:py-7 flex flex-col items-center justify-center text-center gap-3 hover:bg-[#3a3a3a] focus:outline-none focus:ring-2 focus:ring-white/70 focus:ring-offset-2 focus:ring-offset-[#a8ad98]">
                                    <span class="text-white/80">
                                        @if($iconIndex === 0)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 10h10" />
                                                <path d="M8 10a4 4 0 0 1 8 0" />
                                                <path d="M9 10v2a3 3 0 0 0 6 0v-2" />
                                                <path d="M6 14h12" />
                                                <path d="M8 14v6" />
                                                <path d="M16 14v6" />
                                                <path d="M5 20h14" />
                                            </svg>
                                        @elseif($iconIndex === 1)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 6c2 2 2 4 0 6" />
                                                <path d="M12 6c2 2 2 4 0 6" />
                                                <path d="M17 6c2 2 2 4 0 6" />
                                                <path d="M6 18h12" />
                                                <path d="M7 18v-3a5 5 0 0 1 10 0v3" />
                                            </svg>
                                        @elseif($iconIndex === 2)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 20 12 4l9 16" />
                                                <path d="M7 20 12 11l5 9" />
                                                <path d="M10 20v-4h4v4" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-9 w-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
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

<!-- PRODUCT GRID -->
<section id="producten" class="mb-20 md:mb-24">
    <div class="mb-8 md:mb-10">
        <p class="text-sm uppercase tracking-[0.26em] text-gray-600">Curated selectie</p>
        <h2 class="text-2xl md:text-3xl font-semibold text-[#1f2b24]">{{ $productSectionTitle }}</h2>
        <p class="text-sm md:text-base text-gray-600 mt-2 max-w-2xl">{{ $productSectionIntro }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        @forelse ($products as $product)
            <article class="bg-[#f4f1e8] border border-white/50 rounded-3xl shadow-[0_18px_45px_-28px_rgba(0,0,0,0.45)] overflow-hidden flex flex-col">
                <a href="{{ route('product.show', $product->slug) }}" class="block">
                    <div class="aspect-[4/3] bg-[#e3eadf] relative overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-[#1f2b24] text-sm font-semibold px-6 text-center">
                                {{ $product->name }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-white/10"></div>
                    </div>
                </a>

                <div class="p-6 space-y-4 flex-1 flex flex-col">
                    <div class="space-y-2">
                        <h3 class="text-xl font-semibold text-[#1f2b24] leading-snug">
                            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-green-700 transition">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <p class="text-sm md:text-base text-gray-600 line-clamp-3">{{ $product->description ?? 'Geen beschrijving beschikbaar' }}</p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <span class="text-xs font-semibold tracking-[0.16em] text-gray-700 uppercase">€ {{ number_format($product->price, 2, ',', '.') }}</span>

                        <form method="POST" action="{{ route('cart.add', $product->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 underline underline-offset-4 decoration-2 decoration-green-600/50 hover:decoration-green-700 transition">
                                In winkelmand
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
<section class="mb-12 md:mb-16">
    <h2 class="text-2xl md:text-3xl font-semibold text-[#1f2b24] mb-6 md:mb-8">{{ $categoriesTitle }}</h2>

    <div class="bg-[#e9e4d9] rounded-3xl px-4 sm:px-6 md:px-8 py-8 md:py-10 overflow-hidden border border-white/40 shadow-[0_14px_40px_-32px_rgba(0,0,0,0.45)]">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6 md:gap-8">
            @forelse ($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="group relative block rounded-2xl bg-[#2f352f] text-white border border-white/10 shadow-[0_18px_40px_-30px_rgba(0,0,0,0.6)] overflow-hidden transition transform hover:-translate-y-1 hover:shadow-[0_20px_50px_-28px_rgba(0,0,0,0.55)]">
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/5 transition"></div>

                    <div class="relative flex flex-col items-center justify-center gap-3 px-6 sm:px-8 py-8">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white/90 group-hover:border-white/25">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3c-1.5 2.2-.4 3.8.6 5.1.8 1.1 1.4 2 .7 3.6-.6 1.5-2.1 2.3-3.3 2.9-1.3.6-2.1 1.5-2.1 3.1 0 1.6 1.2 3 4.1 3 2.8 0 4.5-1.6 4.5-4 0-1.7-.8-2.5-1.3-3.4-.7-1.2-.6-2.3.3-3.7.9-1.5 1.4-3.4-.5-6.6" />
                            </svg>
                        </span>

                        <div class="text-center space-y-1">
                            <div class="text-lg sm:text-xl font-semibold">{{ $category->name }}</div>
                            <p class="text-sm text-white/80">Bekijk producten</p>
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
<section class="mt-16 md:mt-20">
    <div class="flex items-center justify-between mb-6 md:mb-8">
        <h2 class="text-2xl md:text-3xl font-semibold text-[#1f2b24]">{{ $faqTitle }}</h2>
        <span class="hidden sm:inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">
            Snelle antwoorden
        </span>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @php
            $faqs = $faqs ?? collect();
        @endphp

        @foreach($faqs as $item)
            <details class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition">
                <summary class="cursor-pointer select-none px-5 py-4 font-semibold text-[#1f2b24] flex items-center gap-3">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                        Q
                    </span>
                    <span class="flex-1">{{ $item['q'] ?? '' }}</span>
                    <span class="text-emerald-600 transition-transform duration-200 group-open:rotate-45">+</span>
                </summary>
                <div class="px-5 pb-5 text-gray-700 leading-relaxed">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-bold">
                            A
                        </span>
                        <p class="text-sm md:text-base">{{ $item['a'] ?? '' }}</p>
                    </div>
                </div>
            </details>
        @endforeach
    </div>
</section>



@endsection
