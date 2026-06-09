@extends('themes.default.layouts.app')

@php
    use App\Models\Setting;

    $cmsValue = function (string $key, string $default) {
        $value = Setting::get($key, null);
        if (is_string($value) && trim($value) === '') {
            return $default;
        }
        return $value ?? $default;
    };

    $pageTitle = $cmsValue('informatie_title', 'De ideale verwarmingsoplossing: De pelletkachel');
    $pageIntro = $cmsValue('informatie_intro', 'Een snelle vergelijking van populaire kacheltypes, met focus op rendement, gebruiksgemak en betrouwbaarheid.');

    $b1Title = $cmsValue('informatie_block_1_title', 'De ideale verwarmingsoplossing');
    $b1Text = $cmsValue('informatie_block_1_text', 'In de zoektocht naar de ideale verwarmingsoplossing voor je woonkamer, serre, tuinhuis of andere ruimtes, sta je voor de keuze tussen verschillende soorten kachels. Laserkachels, kouskachels, gaskachels en pelletkachels bieden elk unieke voordelen.');

    $b2Title = $cmsValue('informatie_block_2_title', 'Laserkachels (Zibro & Qlima)');
    $b2Text = $cmsValue('informatie_block_2_text', 'Laserkachels beschikken over een elektronisch gestuurde brander voor snelle opstart en nauwkeurige temperatuurregeling. Met thermostaat en timer stel je eenvoudig de gewenste warmte in. Ze werken efficiënt en geurloos met Petroleum C of witte GTL.');

    $b3Title = $cmsValue('informatie_block_3_title', 'Kouskachels');
    $b3Text = $cmsValue('informatie_block_3_text', 'Kouskachels werken zonder externe stroombron en zijn ideaal voor hobbyruimtes, schuren of campinggebruik. De verbranding is zichtbaar en de ontsteking gebeurt via een gloeispiraal op batterijen, waardoor de kachel blijft werken bij stroomuitval.');

    $b4Title = $cmsValue('informatie_block_4_title', 'Gevelkachels');
    $b4Text = $cmsValue('informatie_block_4_text', 'Gevelkachels van Zibro (Toyotomi) maken gebruik van een “pijp-in-een-pijp” rookafvoersysteem. Hiermee kunnen ruimtes tot 450 m³ veilig worden verwarmd zonder uitlaatgassen in de kamer.');

    $b5Title = $cmsValue('informatie_block_5_title', 'De pelletkachel');
    $b5Text = $cmsValue('informatie_block_5_text', 'Een pelletkachel brandt op houtpellets en regelt automatisch de toevoer. Dankzij het hoge rendement – tot wel 97% – verbruik je minder brandstof voor dezelfde hoeveelheid warmte.');

    $b6Title = $cmsValue('informatie_block_6_title', 'Efficiënt, modern en betrouwbaar');
    $b6Text = $cmsValue('informatie_block_6_text', 'Pelletkachels zijn moderne, computergestuurde apparaten die automatisch temperatuur, pellettoevoer en rookgasafvoer regelen. Bij storingen staat Oliehandel van Deutekom klaar met service op maat.');
@endphp

@section('title', $pageTitle)

@section('content')

<!-- Titel -->
<section class="mb-10 sm:mb-12 md:mb-16">
    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
        {{ $pageTitle }}
    </h1>
    <p class="mt-3 text-gray-600 max-w-3xl text-sm sm:text-base">
        {{ $pageIntro }}
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
            {{ $b1Title }}
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            {{ $b1Text }}
        </p>
    </div>
</section>

<!-- Blok 2: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-12 md:mb-16 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            {{ $b2Title }}
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            {{ $b2Text }}
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
            {{ $b3Title }}
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            {{ $b3Text }}
        </p>
    </div>
</section>

<!-- Blok 4: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-12 md:mb-16 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            {{ $b4Title }}
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            {{ $b4Text }}
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
            {{ $b5Title }}
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            {{ $b5Text }}
        </p>
    </div>
</section>

<!-- Blok 6: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 items-center mb-4 md:mb-10 bg-white border rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm">
    <div class="space-y-3">
        <h2 class="text-lg sm:text-xl font-semibold">
            {{ $b6Title }}
        </h2>
        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
            {{ $b6Text }}
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
