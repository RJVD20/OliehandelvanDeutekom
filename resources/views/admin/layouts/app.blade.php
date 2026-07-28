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
        class="turbo-admin-sidebar fixed inset-y-0 left-0 w-64 text-white flex flex-col px-4 py-6 transform transition-transform duration-200 md:translate-x-0 z-40"
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

            <a href="{{ route('admin.users.index') }}"
               class="turbo-admin-nav flex items-center gap-3 px-3 py-2 rounded
               {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
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
            @yield('content')
        </div>
    </main>

</div>

@yield('scripts')
@stack('scripts')

@include('components.whatsapp-fab')
</body>
</html>
