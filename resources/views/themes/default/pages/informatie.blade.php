@extends('themes.default.layouts.app')

@section('title', 'De ideale verwarmingsoplossing: De pelletkachel')

@section('content')

<!-- Titel -->
<section class="mb-10 sm:mb-12 md:mb-16">
    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
        De ideale verwarmingsoplossing: De pelletkachel
    </h1>
    <p class="mt-3 text-gray-600 max-w-3xl text-sm sm:text-base">
        Een snelle vergelijking van populaire kacheltypes, met focus op rendement, gebruiksgemak en betrouwbaarheid.
    </p>
</section>

<!-- Blok 1: Afbeelding links, tekst rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-12 md:mb-16 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="overflow-hidden rounded-xl">
        <img
            src="/images/informatie/open-haard.jpg"
            alt="Verwarming"
            class="w-full h-full object-cover aspect-video"
        >
    </div>

    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            De ideale verwarmingsoplossing
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            In de zoektocht naar de ideale verwarmingsoplossing voor je woonkamer,
            serre, tuinhuis of andere ruimtes, sta je voor de keuze tussen verschillende
            soorten kachels. Laserkachels, kouskachels, gaskachels en pelletkachels
            bieden elk unieke voordelen.
        </p>
    </div>
</section>

<!-- Blok 2: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-12 md:mb-16 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            Laserkachels (Zibro & Qlima)
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            Laserkachels beschikken over een elektronisch gestuurde brander voor
            snelle opstart en nauwkeurige temperatuurregeling. Met thermostaat en
            timer stel je eenvoudig de gewenste warmte in. Ze werken efficiënt en
            geurloos met Petroleum C of witte GTL.
        </p>
    </div>

    <div class="overflow-hidden rounded-xl">
        <img
            src="/images/informatie/laserkachel.jpg"
            alt="Laserkachel"
            class="w-full h-full object-cover aspect-video"
        >
    </div>
</section>

<!-- Blok 3: Afbeelding links, tekst rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-12 md:mb-16 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="overflow-hidden rounded-xl">
        <img
            src="/images/informatie/kouskachel.jpg"
            alt="Kouskachel"
            class="w-full h-full object-cover aspect-video"
        >
    </div>

    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            Kouskachels
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            Kouskachels werken zonder externe stroombron en zijn ideaal voor
            hobbyruimtes, schuren of campinggebruik. De verbranding is zichtbaar
            en de ontsteking gebeurt via een gloeispiraal op batterijen, waardoor
            de kachel blijft werken bij stroomuitval.
        </p>
    </div>
</section>

<!-- Blok 4: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-12 md:mb-16 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            Gevelkachels
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            Gevelkachels van Zibro (Toyotomi) maken gebruik van een
            “pijp-in-een-pijp” rookafvoersysteem. Hiermee kunnen ruimtes tot
            450 m³ veilig worden verwarmd zonder uitlaatgassen in de kamer.
        </p>
    </div>

    <div class="overflow-hidden rounded-xl">
        <img
            src="/images/informatie/gevelkachel.jpg"
            alt="Gevelkachel"
            class="w-full h-full object-cover aspect-video"
        >
    </div>
</section>

<!-- Blok 5: Afbeelding links, tekst rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-12 md:mb-16 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="overflow-hidden rounded-xl">
        <img
            src="/images/informatie/pelletkachel.jpg"
            alt="Pelletkachel"
            class="w-full h-full object-cover aspect-video"
        >
    </div>

    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            De pelletkachel
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            Een pelletkachel brandt op houtpellets en regelt automatisch de
            toevoer. Dankzij het hoge rendement – tot wel 97% – verbruik je
            minder brandstof voor dezelfde hoeveelheid warmte.
        </p>
    </div>
</section>

<!-- Blok 6: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-4 md:mb-10 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            Efficiënt, modern en betrouwbaar
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            Pelletkachels zijn moderne, computergestuurde apparaten die
            automatisch temperatuur, pellettoevoer en rookgasafvoer regelen.
            Bij storingen staat Oliehandel van Deutekom klaar met service op maat.
        </p>
    </div>

    <div class="overflow-hidden rounded-xl">
        <img
            src="/images/informatie/service.jpg"
            alt="Service"
            class="w-full h-full object-cover aspect-video"
        >
    </div>
</section>

@endsection
