@extends('themes.default.layouts.app')

@section('title', 'Home')

@section('content')

<!-- HERO -->
<section class="mb-12 md:mb-20">
    <div class="bg-white border rounded-2xl shadow-sm px-4 py-8 sm:px-6 md:px-10 md:py-14">
        <div class="text-center space-y-3">
            <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold text-green-700 tracking-tight">
                Duurzaam. Simpel. Groen.
            </h1>
            <p class="text-gray-600 text-base sm:text-lg md:text-xl max-w-3xl mx-auto leading-relaxed px-2">
                Ontdek onze zorgvuldig geselecteerde producten met focus op kwaliteit en duurzaamheid.
            </p>
        </div>
    </div>
</section>

<!-- PRODUCT GRID -->
<section class="mb-20 md:mb-24">
    <h2 class="text-2xl md:text-3xl font-semibold text-green-700 mb-6 md:mb-8">
        Populaire producten
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 md:gap-8">
        @forelse ($products as $product)
            <div class="bg-white border rounded-2xl shadow-sm hover:shadow-md transition group overflow-hidden">

                <!-- Klikbare image / header -->
                <a href="{{ route('product.show', $product->slug) }}"
                   class="block h-48 md:h-56 bg-green-50 flex items-center justify-center
                          group-hover:bg-green-100 transition">
                    <span class="text-green-700 font-semibold text-center px-3">
                        @if($product->image)
    <img
        src="{{ asset('storage/' . $product->image) }}"
        class="h-48 md:h-56 w-full object-cover"
    >
@else
    <div class="h-48 md:h-56 bg-green-50 flex items-center justify-center">
        <span class="text-green-700 text-sm">
            {{ $product->name }}
        </span>
    </div>
@endif
                    </span>
                </a>

                <div class="p-5 md:p-6">

                    <!-- Productnaam (extra klikbaar & SEO-friendly) -->
                    <h3 class="font-semibold text-gray-900 mb-2 text-lg">
                        <a href="{{ route('product.show', $product->slug) }}"
                           class="hover:text-green-700 transition">
                            {{ $product->name }}
                        </a>
                    </h3>

                    <p class="text-sm md:text-base text-gray-500 mb-5 line-clamp-2">
                        {{ $product->description ?? 'Geen beschrijving beschikbaar' }}
                    </p>

                    <div class="flex items-center justify-between">
                        <span class="font-bold text-green-700 text-lg">
                            € {{ number_format($product->price, 2, ',', '.') }}
                        </span>

                        <form method="POST" action="{{ route('cart.add', $product->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 transition w-full sm:w-auto"
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
    <h2 class="text-2xl md:text-3xl font-semibold text-green-700 mb-6 md:mb-8">
        Shop per categorie
    </h2>

    <div class="bg-[#858f7b] rounded-2xl px-4 sm:px-6 md:px-8 py-8 md:py-10 overflow-hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6 md:gap-8">
            @forelse ($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="group relative block rounded-2xl bg-[#3b3b3b] text-white border border-white/5 shadow-lg overflow-hidden transition transform hover:-translate-y-1 hover:shadow-xl">
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/5 transition"></div>

                    <div class="relative flex flex-col items-center justify-center gap-3 px-6 sm:px-8 py-8">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/90 group-hover:border-white/30">
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
                <p class="text-gray-100 col-span-3 text-center">Geen categorieën gevonden.</p>
            @endforelse
        </div>
    </div>
</section>



<!-- FAQ -->
<section class="mt-16 md:mt-20">
    <h2 class="text-2xl md:text-3xl font-semibold text-green-700 mb-6 md:mb-8">
        Veelgestelde vragen
    </h2>

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
            <details class="group bg-white border rounded-2xl shadow-sm">
                <summary class="cursor-pointer select-none px-6 py-4 font-semibold text-gray-900 flex items-center justify-between">
                    <span>{{ $item['q'] }}</span>
                    <span class="text-green-700 transition-transform duration-200 group-open:rotate-45">+</span>
                </summary>
                <div class="px-6 pb-5 text-gray-600 leading-relaxed">
                    {{ $item['a'] }}
                </div>
            </details>
        @endforeach
    </div>
</section>



@endsection
