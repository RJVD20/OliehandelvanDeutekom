<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Webshop')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">

@include('themes.default.components.nav')

<main class="max-w-7xl mx-auto p-6">
    @yield('content')
</main>

</body>
</html>
