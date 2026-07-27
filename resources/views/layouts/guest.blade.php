<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="turbo-auth font-sans text-gray-900 antialiased">
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

        @include('components.whatsapp-fab')
    </body>
</html>
