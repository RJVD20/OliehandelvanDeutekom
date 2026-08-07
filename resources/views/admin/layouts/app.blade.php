<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/webp" sizes="64x64" href="{{ asset('images/favicon-v2.webp') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <title>Admin – @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="turbo-admin" x-data="{ menuOpen: false }">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside
        class="turbo-admin-sidebar fixed inset-y-0 left-0 w-64 -translate-x-full text-white flex flex-col px-4 py-6 transform md:translate-x-0 z-40"
        :class="menuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >

        {{-- Logo / Titel --}}
        <div class="mb-8 flex items-center justify-between md:block">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-turbo-gold">Turbo Heating</p>
                <h1 class="mt-1 text-xl font-bold">Beheeromgeving</h1>
            </div>
            <button type="button" class="md:hidden text-gray-300" @click.stop="menuOpen = false">✕</button>
        </div>

        {{-- Navigatie --}}
        <nav class="flex-1 space-y-1 text-sm overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                📊 Dashboard
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}">
                📦 Bestellingen
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.payments.*') ? 'is-active' : '' }}">
                💳 Betalingen
            </a>

            <a href="{{ route('admin.newsletters.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.newsletters.*') ? 'is-active' : '' }}">
                ✉️ Nieuwsbrieven
            </a>

            @php $routesOpen = request()->routeIs('admin.routes.*'); @endphp
            <div
                x-data="{ open: {{ $routesOpen ? 'true' : 'false' }} }"
                @mouseenter="open = true"
                @mouseleave="open = false"
            >
                <button
                    type="button"
                    @click="open = !open"
                    class="turbo-admin-nav flex w-full items-center gap-3 rounded px-3 py-2 {{ $routesOpen ? 'is-active' : '' }}"
                >
                    <span>🗺️ Routes</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="ml-auto h-3.5 w-3.5 transition-transform duration-150"
                        :class="open ? 'rotate-90' : ''"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition
                    class="mt-0.5 ml-3 space-y-0.5 border-l border-white/10 pl-3"
                >
                    <a href="{{ route('admin.routes.smart') }}"
                       class="turbo-admin-nav flex items-center gap-2 rounded px-3 py-1.5 text-sm {{ request()->routeIs('admin.routes.smart*') ? 'is-active' : '' }}">
                        Slim route plannen
                    </a>
                    <a href="{{ route('admin.routes.index') }}"
                       class="turbo-admin-nav flex items-center gap-2 rounded px-3 py-1.5 text-sm {{ request()->routeIs('admin.routes.index') ? 'is-active' : '' }}">
                        Bestaande routes
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.locations.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.locations.*') ? 'is-active' : '' }}">
                📍 Locaties
            </a>

            <a href="{{ route('admin.content.edit') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.content.*') ? 'is-active' : '' }}">
                ✍️ CMS teksten
            </a>

            {{-- Webshop dropdown --}}
            @php $webshopOpen = request()->routeIs('admin.products.*', 'admin.categories.*'); @endphp
            <div
                x-data="{ open: {{ $webshopOpen ? 'true' : 'false' }} }"
                @mouseenter="open = true"
                @mouseleave="open = false"
            >
                <button
                    type="button"
                    @click="open = !open"
                    class="turbo-admin-nav w-full flex items-center gap-3 px-3 py-2 rounded {{ $webshopOpen ? 'is-active' : '' }}"
                >
                    <span>🛒 Webshop</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="ml-auto h-3.5 w-3.5 transition-transform duration-150"
                        :class="open ? 'rotate-90' : ''"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="mt-0.5 ml-3 pl-3 border-l border-white/10 space-y-0.5"
                >
                    <a href="{{ route('admin.products.index') }}"
                       class="turbo-admin-nav flex items-center gap-2 px-3 py-1.5 rounded text-sm
                       {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}">
                        Producten
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="turbo-admin-nav flex items-center gap-2 px-3 py-1.5 rounded text-sm
                       {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                        Categoriebeheer
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.promotions.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.promotions.*') ? 'is-active' : '' }}">
                🏷️ Acties
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                👥 Gebruikers
            </a>

            <a href="{{ route('admin.audit.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.audit.*') ? 'is-active' : '' }}">
                🕘 Auditlog
            </a>

            <a href="{{ route('admin.help') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.help') ? 'is-active' : '' }}">
                ❓ Help & uitleg
            </a>
        </nav>

        {{-- User info --}}
        <div class="border-t border-gray-700 pt-4 text-xs text-gray-400">
            <div class="flex items-center justify-between">
                <div>
                    Ingelogd als<br>
                    <span class="text-white font-medium">
                        {{ auth()->user()->name }}
                    </span>
                </div>

                <a href="{{ route('home') }}" class="turbo-admin-site-link ml-4 inline-block px-3 py-2 text-sm rounded">Naar site</a>
            </div>
        </div>

    </aside>

    {{-- Overlay for mobile --}}
    <div
        class="fixed inset-0 bg-black/40 z-30 md:hidden"
        x-show="menuOpen"
        x-transition.opacity
        @click.self="menuOpen = false"
        style="display:none;"
    ></div>

    {{-- Content --}}
    <main class="flex-1 md:ml-64 w-full">
        <div class="turbo-admin-mobilebar flex items-center justify-between px-4 py-5 border-b md:hidden">
            <button type="button" class="p-3 rounded-lg bg-gray-100 text-base" @click.stop="menuOpen = true">☰</button>
            <div class="text-base font-semibold text-gray-700">Admin</div>
            <a href="{{ route('home') }}" class="text-base font-semibold text-turbo-blue">Naar site</a>
        </div>
        <div class="turbo-admin-content p-4 sm:p-6 lg:p-8">
            @unless(request()->routeIs('admin.dashboard', 'admin.help'))
                @php
                    $helpSections = [
                        'admin.orders.*' => ['Bestellingen beheren', 'Hier verwerk je bestellingen van binnenkomst tot levering of afhalen.', ['Controleer klant, artikelen en afleverwijze.', 'Controleer en verwerk de betaling.', 'Plan bezorging of rond de bestelling af.'], 'Een statuswijziging is zichtbaar voor de klant en kan e-mails of operationele acties beïnvloeden.'],
                        'admin.payments.*' => ['Betalingen verwerken', 'Bekijk wat betaald, openstaand of verlopen is en leg de echte betaalwijze vast.', ['Filter eerst op openstaand.', 'Controleer bedrag en bestelling.', 'Markeer pas betaald nadat het geld ontvangen is.'], 'Een betaalmarkering verandert de financiële administratie en de status voor de klant.'],
                        'admin.routes.*' => ['Bezorgroutes plannen', 'Groepeer bezorgingen, wijs een chauffeur toe en controleer de lading.', ['Selecteer nog niet geplande bezorgingen.', 'Maak en controleer de route.', 'Gebruik voor vertrek de laadlijst.'], 'Geplande orders verschijnen in de bezorgapp van de toegewezen chauffeur.'],
                        'admin.products.*' => ['Producten beheren', 'Beheer wat klanten in de webshop kunnen bekijken en bestellen.', ['Controleer naam, prijs en afbeelding.', 'Zet het product alleen actief als het compleet is.', 'Bekijk het resultaat op de website.'], 'Actieve producten zijn direct zichtbaar en bestelbaar.'],
                        'admin.categories.*' => ['Categorieën beheren', 'Categorieën bepalen hoe producten in de webshop gegroepeerd worden.', ['Gebruik een duidelijke naam.', 'Koppel de juiste producten.', 'Controleer de webshopnavigatie.'], 'Wijzigingen kunnen de vindbaarheid van meerdere producten tegelijk beïnvloeden.'],
                        'admin.promotions.*' => ['Acties beheren', 'Maak tijdelijke aanbiedingen met een bundelprijs, looptijd en zichtbare kanalen.', ['Kies het hoofdproduct en de extra artikelen.', 'Controleer actieprijs en periode.', 'Activeer en bekijk de actie op de website.'], 'Een actieve actie kan direct prijs, gratis verzending en promotieblokken beïnvloeden.'],
                        'admin.newsletters.*' => ['Nieuwsbrieven beheren', 'Maak en verzend e-mailcampagnes naar geselecteerde klanten.', ['Stel onderwerp en inhoud op.', 'Stuur altijd eerst een testmail.', 'Controleer ontvangers en verzend daarna.'], 'Definitief verzonden e-mail kan niet worden teruggehaald.'],
                        'admin.content.*' => ['Websiteteksten beheren', 'Pas algemene teksten en bedrijfsinstellingen van de openbare website aan.', ['Wijzig alleen het bedoelde veld.', 'Controleer spelling en contactgegevens.', 'Sla op en bekijk de website.'], 'Opgeslagen inhoud kan direct voor bezoekers zichtbaar zijn.'],
                        'admin.locations.*' => ['Locaties beheren', 'Beheer de locatiepagina’s en hun zichtbaarheid.', ['Controleer adres en openingstijden.', 'Voeg een duidelijke beschrijving toe.', 'Zet pas zichtbaar wanneer alles klopt.'], 'Zichtbare locaties worden direct op de openbare website getoond.'],
                        'admin.users.*' => ['Gebruikers beheren', 'Bekijk accounts en beheer met zorg wie toegang tot dit adminpaneel heeft.', ['Zoek de juiste gebruiker.', 'Controleer naam en e-mailadres.', 'Wijzig beheerrechten alleen bewust.'], 'Een beheerder krijgt toegang tot klant-, bestel- en betaalgegevens.'],
                        'admin.audit.*' => ['Wijzigingen controleren', 'Bekijk wie belangrijke handelingen in het adminpaneel heeft uitgevoerd.', ['Filter op onderwerp of beheerder.', 'Open het betreffende onderdeel.', 'Corrigeer onbedoelde wijzigingen waar nodig.'], null],
                    ];
                    $pageHelp = collect($helpSections)->first(fn ($value, $pattern) => request()->routeIs($pattern));
                @endphp
                @if($pageHelp)
                    <x-admin.page-help :title="$pageHelp[0]" :intro="$pageHelp[1]" :steps="$pageHelp[2]" :impact="$pageHelp[3]" />
                @endif
            @endunless
            @yield('content')
        </div>
    </main>

</div>

@yield('scripts')
@stack('scripts')

@include('components.whatsapp-fab')
</body>
</html>
