@extends('themes.default.layouts.app')

@section('title', 'Home')

@section('content')

<!-- HERO -->
<section class="relative overflow-hidden mb-16 md:mb-24">
    <div class="absolute inset-0 bg-gradient-to-r from-[#f6f2ea] via-[#eef2eb] to-[#e7ede5]"></div>
    <div class="absolute -left-10 -top-16 h-64 w-64 bg-gradient-to-br from-[#c8d5c5]/50 to-transparent blur-3xl opacity-80"></div>
    <div class="absolute right-0 bottom-0 h-80 w-80 bg-gradient-to-tl from-[#a9b8a2]/40 to-transparent blur-3xl opacity-70"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 md:px-10 py-16 md:py-20 lg:py-24 flex flex-col lg:flex-row items-center gap-10 lg:gap-14">
        <div class="w-full lg:w-3/5 space-y-5">
            <p class="text-sm uppercase tracking-[0.28em] text-gray-600">Established specialist</p>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold leading-tight text-[#1f2b24]">
                Zorgvuldig geselecteerde energieproducten voor professionals en particulier.
            </h1>
            <p class="text-base sm:text-lg text-gray-700 max-w-2xl leading-relaxed">
                Rustige, vakmatige begeleiding bij elke bestelling. Duurzaam waar het telt, met materiaal- en productspecialisten die weten wat werkt.
            </p>
            <div class="pt-2">
                <a href="#producten" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-[#1f2b24] text-[#1f2b24] font-semibold text-sm tracking-wide hover:bg-[#1f2b24] hover:text-white transition">
                    Ontdek onze selectie
                    <span aria-hidden="true" class="text-base">→</span>
                </a>
            </div>
        </div>

        <div class="w-full lg:w-2/5 relative">
            <div class="absolute -left-6 -top-6 h-16 w-16 rounded-full bg-[#dce4d6] blur-lg opacity-60"></div>
            <div class="absolute -right-10 bottom-10 h-24 w-24 rounded-full bg-[#c7d1c2] blur-2xl opacity-60"></div>
            <div class="relative aspect-[4/3] w-full rounded-[28px] overflow-hidden bg-gradient-to-br from-[#e9eee7] via-[#dfe6dd] to-[#d7dfd4] shadow-[0_25px_60px_-40px_rgba(0,0,0,0.45)] border border-white/40">
                <div class="absolute inset-0 mix-blend-multiply bg-gradient-to-tr from-black/10 via-transparent to-white/30"></div>
                <div class="absolute inset-6 rounded-2xl bg-white/40 backdrop-blur-sm border border-white/30"></div>
                <div class="absolute inset-0 flex items-center justify-center px-8 text-center text-[#1f2b24] font-semibold leading-relaxed">
                    Een rustige sfeerimpressie van de collectie: zachte kleuren, afgeronde vormen en focus op materiaal.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCT GRID -->
<section id="producten" class="mb-20 md:mb-24">
    <div class="mb-8 md:mb-10">
        <p class="text-sm uppercase tracking-[0.26em] text-gray-600">Curated selectie</p>
        <h2 class="text-2xl md:text-3xl font-semibold text-[#1f2b24]">Populaire producten</h2>
        <p class="text-sm md:text-base text-gray-600 mt-2 max-w-2xl">Rustige, zorgvuldig gekozen items met focus op kwaliteit en betrouwbaarheid.</p>
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
    <h2 class="text-2xl md:text-3xl font-semibold text-[#1f2b24] mb-6 md:mb-8">Shop per categorie</h2>

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
    <h2 class="text-2xl md:text-3xl font-semibold text-[#1f2b24] mb-6 md:mb-8">Veelgestelde vragen</h2>

    <div class="space-y-3">
        @php
            $faqs = [
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
        @endphp

        @foreach($faqs as $item)
            <details class="group bg-[#f4f1e8] border border-white/60 rounded-2xl shadow-[0_14px_35px_-32px_rgba(0,0,0,0.45)]">
                <summary class="cursor-pointer select-none px-6 py-4 font-semibold text-[#1f2b24] flex items-center justify-between">
                    <span>{{ $item['q'] }}</span>
                    <span class="text-green-700 transition-transform duration-200 group-open:rotate-45">+</span>
                </summary>
                <div class="px-6 pb-5 text-gray-700 leading-relaxed">
                    {{ $item['a'] }}
                </div>
            </details>
        @endforeach
    </div>
</section>



@endsection
