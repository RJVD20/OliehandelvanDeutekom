@extends('themes.default.layouts.app')

@section('title', 'De ideale verwarmingsoplossing: De pelletkachel')

@section('content')

<!-- Titel -->
<section class="mb-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        De ideale verwarmingsoplossing: De pelletkachel
    </h1>
</section>

<!-- Blok 1: Afbeelding links, tekst rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <img
        src="/images/informatie/open-haard.jpg"
        alt="Verwarming"
        class="w-full rounded-lg"
    >

    <div>
        <h2 class="text-xl font-semibold mb-4">
            De ideale verwarmingsoplossing
        </h2>
        <p class="text-gray-700 leading-relaxed">
            In de zoektocht naar de ideale verwarmingsoplossing voor je woonkamer,
            serre, tuinhuis of andere ruimtes, sta je voor de keuze tussen verschillende
            soorten kachels. Laserkachels, kouskachels, gaskachels en pelletkachels
            bieden elk unieke voordelen.
        </p>
    </div>
</section>

<!-- Blok 2: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <div>
        <h2 class="text-xl font-semibold mb-4">
            Laserkachels (Zibro & Qlima)
        </h2>
        <p class="text-gray-700 leading-relaxed">
            Laserkachels beschikken over een elektronisch gestuurde brander voor
            snelle opstart en nauwkeurige temperatuurregeling. Met thermostaat en
            timer stel je eenvoudig de gewenste warmte in. Ze werken efficiënt en
            geurloos met Petroleum C of witte GTL.
        </p>
    </div>

    <img
        src="/images/informatie/laserkachel.jpg"
        alt="Laserkachel"
        class="w-full rounded-lg"
    >
</section>

<!-- Blok 3: Afbeelding links, tekst rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <img
        src="/images/informatie/kouskachel.jpg"
        alt="Kouskachel"
        class="w-full rounded-lg"
    >

    <div>
        <h2 class="text-xl font-semibold mb-4">
            Kouskachels
        </h2>
        <p class="text-gray-700 leading-relaxed">
            Kouskachels werken zonder externe stroombron en zijn ideaal voor
            hobbyruimtes, schuren of campinggebruik. De verbranding is zichtbaar
            en de ontsteking gebeurt via een gloeispiraal op batterijen, waardoor
            de kachel blijft werken bij stroomuitval.
        </p>
    </div>
</section>

<!-- Blok 4: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <div>
        <h2 class="text-xl font-semibold mb-4">
            Gevelkachels
        </h2>
        <p class="text-gray-700 leading-relaxed">
            Gevelkachels van Zibro (Toyotomi) maken gebruik van een
            “pijp-in-een-pijp” rookafvoersysteem. Hiermee kunnen ruimtes tot
            450 m³ veilig worden verwarmd zonder uitlaatgassen in de kamer.
        </p>
    </div>

    <img
        src="/images/informatie/gevelkachel.jpg"
        alt="Gevelkachel"
        class="w-full rounded-lg"
    >
</section>

<!-- Blok 5: Afbeelding links, tekst rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <img
        src="/images/informatie/pelletkachel.jpg"
        alt="Pelletkachel"
        class="w-full rounded-lg"
    >

    <div>
        <h2 class="text-xl font-semibold mb-4">
            De pelletkachel
        </h2>
        <p class="text-gray-700 leading-relaxed">
            Een pelletkachel brandt op houtpellets en regelt automatisch de
            toevoer. Dankzij het hoge rendement – tot wel 97% – verbruik je
            minder brandstof voor dezelfde hoeveelheid warmte.
        </p>
    </div>
</section>

<!-- Blok 6: Tekst links, afbeelding rechts -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <div>
        <h2 class="text-xl font-semibold mb-4">
            Efficiënt, modern en betrouwbaar
        </h2>
        <p class="text-gray-700 leading-relaxed">
            Pelletkachels zijn moderne, computergestuurde apparaten die
            automatisch temperatuur, pellettoevoer en rookgasafvoer regelen.
            Bij storingen staat Oliehandel van Deutekom klaar met service op maat.
        </p>
    </div>

    <img
        src="/images/informatie/service.jpg"
        alt="Service"
        class="w-full rounded-lg"
    >
</section>

@endsection
