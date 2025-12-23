<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin – @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col px-4 py-6">

        {{-- Logo / Titel --}}
        <div class="mb-8">
            <h1 class="text-xl font-bold">Admin</h1>
            <p class="text-xs text-gray-400">Beheeromgeving</p>
        </div>

        {{-- Navigatie --}}
        <nav class="flex-1 space-y-1 text-sm">
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

    {{-- Content --}}
    <main class="flex-1 p-8">
        @yield('content')
    </main>

</div>

@yield('scripts')
</body>
</html>
