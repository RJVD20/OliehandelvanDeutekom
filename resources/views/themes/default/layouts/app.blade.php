<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    @yield('meta')
    <title>@yield('title', 'Webshop')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">

@include('themes.default.components.nav')

<main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    @yield('content')
</main>

@include('themes.default.components.footer')

<div
    x-data="{
        show: {{ session()->has('toast') ? 'true' : 'false' }},
        message: '{{ session('toast') }}'
    }"
    x-init="
        if (show) {
            setTimeout(() => show = false, 3000);
        }
    "
    x-show="show"
    x-transition
    class="fixed top-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50"
    style="display: none;"
>
    <span x-text="message"></span>
</div>



</body>
</html>
