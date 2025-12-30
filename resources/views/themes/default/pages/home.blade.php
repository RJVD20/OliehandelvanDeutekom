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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 md:gap-8">
        @forelse ($categories as $category)
          <a href="{{ route('category.show', $category->slug) }}"
                        class="group bg-green-50 border border-green-100 rounded-2xl p-6 sm:p-8 md:p-10 text-center hover:bg-green-100 transition cursor-pointer">
                            <div class="text-green-700 text-lg sm:text-xl font-semibold mb-2">
                  {{ $category->name }}
              </div>
                            <p class="text-sm md:text-base text-green-600">
                  Bekijk producten
              </p>
          </a>
        @empty
            <p class="text-gray-500 col-span-4 text-center">
                Geen categorieën gevonden.
            </p>
        @endforelse
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
