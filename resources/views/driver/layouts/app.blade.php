<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b0b0c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Route App">

    <title>@yield('title', 'Route App')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --app-bg: #0b0b0c;
            --app-card: #121316;
            --app-card-2: #16181d;
            --app-accent: #60e06f;
            --app-muted: #9aa0ab;
            --app-border: #23262d;
        }

        body {
            font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            background: radial-gradient(1200px 600px at 15% -10%, #1b1d22 0%, transparent 60%),
                        radial-gradient(1000px 500px at 90% -20%, #1f2a22 0%, transparent 65%),
                        var(--app-bg);
        }

        .app-glow {
            box-shadow: 0 10px 30px rgba(96, 224, 111, 0.12), 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        .app-fade-in {
            animation: appFade 500ms ease-out both;
        }

        .driver-date {
            -webkit-appearance: none;
            appearance: none;
            color: #ffffff;
            background-color: var(--app-card-2);
        }

        .driver-date::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.8;
        }

        @keyframes appFade {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen text-white">
    <main class="min-h-screen px-4 pt-6 pb-10 sm:px-6">
        @yield('content')
    </main>
</body>
</html>
