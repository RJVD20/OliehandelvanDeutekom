<!DOCTYPE html>
<html>
<head>
    <title>Admin - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <aside class="w-64 bg-gray-900 text-white p-6">
        <h2 class="text-lg font-bold mb-6">Admin</h2>

        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.orders.index') }}">Bestellingen</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        @yield('content')
    </main>

</div>

</body>
</html>
