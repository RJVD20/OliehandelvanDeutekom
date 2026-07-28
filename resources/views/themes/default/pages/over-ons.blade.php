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

    $heroTitle = $cmsValue('over_hero_title', 'Betrouwbare warmte, eerlijk advies');
    $heroIntro = $cmsValue('over_hero_intro', 'Kachelvloeistof.nl is een familiebedrijf met passie voor warmte-oplossingen. We leveren kachelvloeistoffen, kachels en toebehoren met focus op kwaliteit, duidelijke communicatie en service.');

    $usp1Title = $cmsValue('over_usp_1_title', 'Bezorging op afspraak');
    $usp1Text = $cmsValue('over_usp_1_text', 'We stemmen de levering af zodat je weet waar je aan toe bent.');
    $usp2Title = $cmsValue('over_usp_2_title', 'Klantgericht advies');
    $usp2Text = $cmsValue('over_usp_2_text', 'Hulp nodig bij de juiste keuze? We denken graag met je mee.');
    $usp3Title = $cmsValue('over_usp_3_title', 'Kwaliteit & transparantie');
    $usp3Text = $cmsValue('over_usp_3_text', 'Heldere informatie, eerlijke prijzen en producten waar we achter staan.');
    $usp4Title = $cmsValue('over_usp_4_title', 'Voor particulieren & bedrijven');
    $usp4Text = $cmsValue('over_usp_4_text', 'Voor elke situatie een passende oplossing—van brandstof tot toebehoren.');

    $story1Title = $cmsValue('over_story_1_title', 'Wat je van ons mag verwachten');
    $story1Text1 = $cmsValue('over_story_1_text_1', 'We houden het graag simpel: goede producten, duidelijke afspraken en service. Of je nu zoekt naar kachelvloeistof, een kachel of de juiste toebehoren— wij helpen je op weg.');
    $story1Text2 = $cmsValue('over_story_1_text_2', 'We werken met een overzichtelijk assortiment en kijken met je mee naar de beste keuze voor jouw situatie.');

    $story2Title = $cmsValue('over_story_2_title', 'Duurzaamheid en hergebruik');
    $story2Text1 = $cmsValue('over_story_2_text_1', 'We streven naar zo min mogelijk verspilling en denken graag mee in praktische oplossingen. Heb je vragen over gebruik, opslag of de beste toepassing van producten? Neem contact op.');
    $story2Text2 = $cmsValue('over_story_2_text_2', 'Meer weten? Bekijk ook onze informatiepagina met uitleg over verschillende soorten kachels.');

    $ctaTitle = $cmsValue('over_cta_title', 'Heb je een vraag?');
    $ctaText = $cmsValue('over_cta_text', 'Mail ons gerust. We reageren zo snel mogelijk.');
    $ctaButton = $cmsValue('over_cta_button', 'Neem contact op');
@endphp

@section('title', 'Over ons')
@section('description', 'Maak kennis met Kachelvloeistof.nl: betrouwbare kachelbrandstoffen, kachels, eerlijk advies en persoonlijke service voor particulieren en bedrijven.')

@section('content')

<!-- HERO -->
<section class="mb-10 md:mb-20">
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="px-5 py-7 sm:px-6 sm:py-10 md:px-10 md:py-14">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-green-50 text-green-700 border border-green-100">
                    Over ons
                </span>

                <h1 class="mt-3 sm:mt-4 text-3xl sm:text-4xl md:text-5xl font-bold leading-tight tracking-tight text-gray-900">
                    {{ $heroTitle }}
                </h1>

                <p class="mt-4 max-w-prose text-[15px] sm:text-lg leading-7 sm:leading-relaxed text-gray-600">
                    {{ $heroIntro }}
                </p>

                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row gap-3">
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex justify-center items-center px-5 sm:px-6 py-3 bg-green-600 text-white text-sm sm:text-base font-semibold rounded-lg hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                    >
                        Bekijk producten
                    </a>

                    <a
                        href="{{ route('locaties') }}"
                        class="inline-flex justify-center items-center px-5 sm:px-6 py-3 bg-white text-gray-900 text-sm sm:text-base font-semibold rounded-lg border hover:bg-gray-50 transition"
                    >
                        Ophaallocaties
                    </a>
                </div>
            </div>

            <div class="bg-green-50">
                <img
                    src="/images/informatie/service.jpg"
                    alt="Service"
                    class="w-full h-52 sm:h-64 lg:h-full object-cover"
                    loading="lazy"
                >
            </div>
        </div>
    </div>
</section>

<!-- USP / WAARDEN -->
<section class="mb-10 md:mb-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 md:gap-8">
        <div class="bg-white border border-l-4 border-l-turbo-gold rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 md:p-7">
            <div class="text-green-700 text-sm sm:text-base font-semibold">{{ $usp1Title }}</div>
            <p class="text-gray-600 mt-1.5 text-[14px] sm:text-base leading-6 sm:leading-relaxed">
                {{ $usp1Text }}
            </p>
        </div>

        <div class="bg-white border border-l-4 border-l-turbo-gold rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 md:p-7">
            <div class="text-green-700 text-sm sm:text-base font-semibold">{{ $usp2Title }}</div>
            <p class="text-gray-600 mt-1.5 text-[14px] sm:text-base leading-6 sm:leading-relaxed">
                {{ $usp2Text }}
            </p>
        </div>

        <div class="bg-white border border-l-4 border-l-turbo-gold rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 md:p-7">
            <div class="text-green-700 text-sm sm:text-base font-semibold">{{ $usp3Title }}</div>
            <p class="text-gray-600 mt-1.5 text-[14px] sm:text-base leading-6 sm:leading-relaxed">
                {{ $usp3Text }}
            </p>
        </div>

        <div class="bg-white border border-l-4 border-l-turbo-gold rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 md:p-7">
            <div class="text-green-700 text-sm sm:text-base font-semibold">{{ $usp4Title }}</div>
            <p class="text-gray-600 mt-1.5 text-[14px] sm:text-base leading-6 sm:leading-relaxed">
                {{ $usp4Text }}
            </p>
        </div>
    </div>
</section>

<!-- VERHAAL (AFWISSELEN) -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 lg:gap-12 items-center mb-10 md:mb-20">
    <img
        src="/images/informatie/laserkachel.jpg"
        alt="Kachels"
        class="w-full max-h-64 sm:max-h-none object-cover rounded-xl sm:rounded-2xl border bg-white"
        loading="lazy"
    >

    <div>
        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold leading-snug text-gray-900 mb-3 sm:mb-4">
            {{ $story1Title }}
        </h2>
        <p class="max-w-prose text-[15px] sm:text-base text-gray-700 leading-7">
            {{ $story1Text1 }}
        </p>
        <p class="max-w-prose text-[15px] sm:text-base text-gray-700 leading-7 mt-3 sm:mt-4">
            {{ $story1Text2 }}
        </p>
    </div>
</section>

<section class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 lg:gap-12 items-center mb-10 md:mb-20">
    <div>
        <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold leading-snug text-gray-900 mb-3 sm:mb-4">
            {{ $story2Title }}
        </h2>
        <p class="max-w-prose text-[15px] sm:text-base text-gray-700 leading-7">
            {{ $story2Text1 }}
        </p>
        <p class="max-w-prose text-[15px] sm:text-base text-gray-700 leading-7 mt-3 sm:mt-4">
            {{ $story2Text2 }}
        </p>

        <div class="mt-4 sm:mt-6">
            <a href="{{ route('informatie') }}" class="text-green-700 font-semibold hover:underline">
                Naar informatie
            </a>
        </div>
    </div>

    <img
        src="/images/informatie/pelletkachel.jpg"
        alt="Duurzame oplossingen"
        class="w-full max-h-64 sm:max-h-none object-cover rounded-xl sm:rounded-2xl border bg-white"
        loading="lazy"
    >
</section>

<!-- CTA / CONTACT -->
<section class="mb-6">
    <div class="bg-white border rounded-xl sm:rounded-2xl shadow-sm p-5 sm:p-8 md:p-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 sm:gap-6">
            <div>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold text-gray-900">
                    {{ $ctaTitle }}
                </h2>
                <p class="text-[15px] sm:text-base text-gray-600 mt-1.5 sm:mt-2 leading-7">
                    {{ $ctaText }}
                </p>
            </div>

            <a
                href="mailto:info@kachelvloeistof.nl"
                class="inline-flex justify-center items-center px-5 sm:px-6 py-3 bg-green-600 text-white text-sm sm:text-base font-semibold rounded-lg hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
            >
                {{ $ctaButton }}
            </a>
        </div>
    </div>
</section>

@endsection
