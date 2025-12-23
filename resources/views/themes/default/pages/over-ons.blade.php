@extends('themes.default.layouts.app')

@section('title', 'Over ons')

@section('content')

<!-- HERO -->
<section class="mb-16 md:mb-20">
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="px-6 py-10 md:px-10 md:py-14">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-50 text-green-700 border border-green-100">
                    Over ons
                </span>

                <h1 class="mt-4 text-4xl md:text-5xl font-bold tracking-tight text-gray-900">
                    Betrouwbare warmte, eerlijk advies
                </h1>

                <p class="mt-4 text-gray-600 text-lg leading-relaxed">
                    Oliehandel van Deutekom is een familiebedrijf met passie voor warmte-oplossingen.
                    We leveren kachelvloeistoffen, kachels en toebehoren met focus op kwaliteit,
                    duidelijke communicatie en service.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex justify-center items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                    >
                        Bekijk producten
                    </a>

                    <a
                        href="{{ route('locaties') }}"
                        class="inline-flex justify-center items-center px-6 py-3 bg-white text-gray-900 font-semibold rounded-lg border hover:bg-gray-50 transition"
                    >
                        Ophaallocaties
                    </a>
                </div>
            </div>

            <div class="bg-green-50">
                <img
                    src="/images/informatie/service.jpg"
                    alt="Service"
                    class="w-full h-64 lg:h-full object-cover"
                    loading="lazy"
                >
            </div>
        </div>
    </div>
</section>

<!-- USP / WAARDEN -->
<section class="mb-16 md:mb-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        <div class="bg-white border rounded-2xl shadow-sm p-6 md:p-7">
            <div class="text-green-700 font-semibold">Bezorging op afspraak</div>
            <p class="text-gray-600 mt-2 leading-relaxed">
                We stemmen de levering af zodat je weet waar je aan toe bent.
            </p>
        </div>

        <div class="bg-white border rounded-2xl shadow-sm p-6 md:p-7">
            <div class="text-green-700 font-semibold">Klantgericht advies</div>
            <p class="text-gray-600 mt-2 leading-relaxed">
                Hulp nodig bij de juiste keuze? We denken graag met je mee.
            </p>
        </div>

        <div class="bg-white border rounded-2xl shadow-sm p-6 md:p-7">
            <div class="text-green-700 font-semibold">Kwaliteit & transparantie</div>
            <p class="text-gray-600 mt-2 leading-relaxed">
                Heldere informatie, eerlijke prijzen en producten waar we achter staan.
            </p>
        </div>

        <div class="bg-white border rounded-2xl shadow-sm p-6 md:p-7">
            <div class="text-green-700 font-semibold">Voor particulieren & bedrijven</div>
            <p class="text-gray-600 mt-2 leading-relaxed">
                Voor elke situatie een passende oplossing—van brandstof tot toebehoren.
            </p>
        </div>
    </div>
</section>

<!-- VERHAAL (AFWISSELEN) -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16 md:mb-20">
    <img
        src="/images/informatie/laserkachel.jpg"
        alt="Kachels"
        class="w-full rounded-2xl border bg-white"
        loading="lazy"
    >

    <div>
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-4">
            Wat je van ons mag verwachten
        </h2>
        <p class="text-gray-700 leading-relaxed">
            We houden het graag simpel: goede producten, duidelijke afspraken en service.
            Of je nu zoekt naar kachelvloeistof, een kachel of de juiste toebehoren—
            wij helpen je op weg.
        </p>
        <p class="text-gray-700 leading-relaxed mt-4">
            We werken met een overzichtelijk assortiment en kijken met je mee naar de beste keuze
            voor jouw situatie.
        </p>
    </div>
</section>

<section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16 md:mb-20">
    <div>
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-4">
            Duurzaamheid en hergebruik
        </h2>
        <p class="text-gray-700 leading-relaxed">
            We streven naar zo min mogelijk verspilling en denken graag mee in praktische oplossingen.
            Heb je vragen over gebruik, opslag of de beste toepassing van producten? Neem contact op.
        </p>
        <p class="text-gray-700 leading-relaxed mt-4">
            Meer weten? Bekijk ook onze informatiepagina met uitleg over verschillende soorten kachels.
        </p>

        <div class="mt-6">
            <a href="{{ route('informatie') }}" class="text-green-700 font-semibold hover:underline">
                Naar informatie
            </a>
        </div>
    </div>

    <img
        src="/images/informatie/pelletkachel.jpg"
        alt="Duurzame oplossingen"
        class="w-full rounded-2xl border bg-white"
        loading="lazy"
    >
</section>

<!-- CTA / CONTACT -->
<section class="mb-6">
    <div class="bg-white border rounded-2xl shadow-sm p-8 md:p-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900">
                    Heb je een vraag?
                </h2>
                <p class="text-gray-600 mt-2 leading-relaxed">
                    Mail ons gerust. We reageren zo snel mogelijk.
                </p>
            </div>

            <a
                href="mailto:info@oliehandelvandeutekom.nl"
                class="inline-flex justify-center items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
            >
                Neem contact op
            </a>
        </div>
    </div>
</section>

@endsection
