<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin – @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-gray-100 text-gray-900" x-data="{ menuOpen: false }">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col px-4 py-6 transform transition-transform duration-200 md:translate-x-0 z-40"
        :class="menuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >

        {{-- Logo / Titel --}}
        <div class="mb-8 flex items-center justify-between md:block">
            <div>
                <h1 class="text-xl font-bold">Admin</h1>
                <p class="text-xs text-gray-400">Beheeromgeving</p>
            </div>
            <button class="md:hidden text-gray-300" @click="menuOpen = false">✕</button>
        </div>

        {{-- Navigatie --}}
        <nav class="flex-1 space-y-1 text-sm overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                📊 Dashboard
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                📦 Bestellingen
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.payments.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                💳 Betalingen
            </a>

            <a href="{{ route('admin.newsletters.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.newsletters.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                ✉️ Nieuwsbrieven
            </a>

            <a href="{{ route('admin.routes.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.routes.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                🗺️ Routes
            </a>

            <a href="{{ route('admin.locations.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.locations.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                📍 Locaties
            </a>

            <a href="{{ route('admin.content.edit') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.content.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                ✍️ CMS teksten
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                🛒 Producten
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                👥 Gebruikers
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

                <a href="{{ route('home') }}" class="ml-4 inline-block px-3 py-2 bg-green-600 text-white text-sm rounded">Naar site</a>
            </div>
        </div>

    </aside>

    {{-- Overlay for mobile --}}
    <div
        class="fixed inset-0 bg-black/40 z-30 md:hidden"
        x-show="menuOpen"
        x-transition.opacity
        @click="menuOpen = false"
        style="display:none;"
    ></div>

    {{-- Content --}}
    <main class="flex-1 md:ml-64 w-full">
        <div class="flex items-center justify-between px-4 py-5 border-b bg-white md:hidden">
            <button class="p-3 rounded-lg bg-gray-100 text-base" @click="menuOpen = true">☰</button>
            <div class="text-base font-semibold text-gray-700">Admin</div>
            <a href="{{ route('home') }}" class="text-base text-green-700">Naar site</a>
        </div>
        <div class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

</div>

@yield('scripts')
@stack('scripts')

@include('components.whatsapp-fab')
</body>
</html>
