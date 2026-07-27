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
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8">
            <div>
                <a href="/" class="relative block h-24 w-72 overflow-hidden rounded-xl border border-white/50 bg-white/95 shadow-sm sm:w-80">
                    <img src="/images/logo-kachels-vloeistoffen.webp" alt="Kachels & Vloeistoffen" class="absolute left-1/2 top-1/2 h-auto w-full max-w-none -translate-x-1/2 -translate-y-[45%]">
                </a>
            </div>

            <div class="turbo-auth-card w-full sm:max-w-md mt-7 px-6 py-6 sm:px-8 sm:py-8 overflow-hidden">
                {{ $slot }}
            </div>
        </div>

        @include('components.whatsapp-fab')
    </body>
</html>
