<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/webp" sizes="64x64" href="{{ asset('images/favicon-v2.webp') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="turbo-auth font-sans text-gray-900 antialiased">
        @if (request()->routeIs('login', 'password.request', 'password.reset', 'register', 'verification.notice'))
            <main class="turbo-login-shell {{ request()->routeIs('register') ? 'turbo-register-shell' : '' }}">
                <a href="/" class="turbo-login-logo" aria-label="Terug naar de homepage">
                    <img src="/images/logo-kachels-vloeistoffen.webp" alt="Kachels & Vloeistoffen">
                </a>

                <div class="turbo-login-card {{ request()->routeIs('register') ? 'turbo-register-card' : '' }}">
                    <aside class="turbo-login-intro" aria-label="Over onze service">
                        <div class="turbo-login-intro__glow" aria-hidden="true"></div>

                        <div class="relative z-10">
                            @if (request()->routeIs('register'))
                                <span class="turbo-login-eyebrow">Word klant</span>
                                <h2>Je account in een paar eenvoudige stappen.</h2>
                                <p>Bewaar je gegevens veilig, houd bestellingen bij en rond een volgende bestelling sneller af.</p>
                            @elseif (request()->routeIs('verification.notice'))
                                <span class="turbo-login-eyebrow">Nog één stap</span>
                                <h2>Bevestig je e-mailadres en je account is klaar.</h2>
                                <p>Zo weten we zeker dat jij toegang hebt tot het opgegeven e-mailadres en houden we je account veilig.</p>
                            @elseif (request()->routeIs('password.request', 'password.reset'))
                                <span class="turbo-login-eyebrow">Veilig herstellen</span>
                                <h2>Zo heb je snel weer toegang tot je account.</h2>
                                <p>{{ request()->routeIs('password.reset') ? 'Kies een sterk, uniek wachtwoord dat je niet voor andere accounts gebruikt.' : 'Vul het e-mailadres van je account in. We sturen je een beveiligde en tijdelijk geldige herstellink.' }}</p>
                            @else
                                <span class="turbo-login-eyebrow">Welkom terug</span>
                                <h2>Alles voor een warm huis, binnen handbereik.</h2>
                                <p>Bekijk je bestellingen, beheer je gegevens en reken de volgende keer sneller af.</p>
                            @endif
                        </div>

                        <ul class="turbo-login-benefits relative z-10" aria-label="Voordelen van je account">
                            <li>
                                <span aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m5 12 4 4L19 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                Inzicht in je bestellingen
                            </li>
                            <li>
                                <span aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m5 12 4 4L19 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                Snel en eenvoudig afrekenen
                            </li>
                            <li>
                                <span aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m5 12 4 4L19 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                Persoonlijke gegevens veilig beheerd
                            </li>
                        </ul>
                    </aside>

                    <section class="turbo-login-form">
                        {{ $slot }}
                    </section>
                </div>

                <p class="turbo-login-footer">Veilig inloggen bij Kachels &amp; Vloeistoffen</p>
            </main>
        @else
            <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8">
                <div>
                    <a href="/" class="inline-flex items-center justify-center rounded-2xl border border-white/80 bg-white px-5 py-3 shadow-lg shadow-black/10 sm:px-7 sm:py-4">
                        <img src="/images/logo-kachels-vloeistoffen.webp" alt="Kachels & Vloeistoffen" class="h-auto w-64 sm:w-80 object-contain">
                    </a>
                </div>

                <div class="turbo-auth-card w-full sm:max-w-md mt-7 px-6 py-6 sm:px-8 sm:py-8 overflow-hidden">
                    {{ $slot }}
                </div>
            </div>
        @endif

        @include('components.whatsapp-fab')
    </body>
</html>
