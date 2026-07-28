<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/webp" sizes="64x64" href="{{ asset('images/favicon-v2.webp') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <title>Onderhoud</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="turbo-auth min-h-screen text-gray-900">
    <div class="fixed top-4 right-4" style="position:fixed;top:16px;right:16px;z-index:50;">
        <a
            href="{{ url('/login') }}"
            class="inline-block px-4 py-2 bg-turbo-gold text-turbo-navy rounded"
        >
            Admin inloggen
        </a>
    </div>

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="turbo-auth-card w-full max-w-xl p-8">
            <h1 class="text-2xl font-bold mb-2">We zijn even bezig met onderhoud</h1>
            <p class="text-gray-600 mb-6">
                De website is tijdelijk niet beschikbaar. Probeer het later opnieuw.
            </p>

            <div class="flex items-center gap-3">
                <a
                    href="{{ url('/') }}"
                    class="inline-block px-4 py-2 bg-gray-200 rounded"
                    style="display:inline-block;padding:10px 14px;background:#e5e7eb;color:#111827;text-decoration:none;border-radius:6px;"
                >
                    Opnieuw proberen
                </a>
            </div>
        </div>
    </div>
</body>
</html>
