<!DOCTYPE html>
<html lang="nl">
<head>
    @php
        $seoTitle = trim($__env->yieldContent('title', 'Kachelvloeistof en petroleum kopen'));
        $seoDescription = trim($__env->yieldContent(
            'description',
            'Bestel kachelvloeistof, petroleum, kachels en toebehoren bij Kachelvloeistof.nl. Betrouwbare producten, staffelprijzen en bezorging door heel Nederland.'
        ));
        $isDevelopmentHost = str_starts_with(request()->getHost(), 'dev.')
            || app()->environment(['local', 'development', 'testing']);
        $privateRoute = request()->routeIs([
            'cart.*',
            'checkout.*',
            'login',
            'register',
            'password.*',
            'verification.*',
            'profile.*',
            'account.*',
            'dashboard',
            'admin.*',
            'driver.*',
            'payment.*',
        ]);
        $shouldNoIndex = $isDevelopmentHost || $privateRoute || request()->query->count() > 0;
        $canonicalUrl = url()->current();
        $socialImage = trim($__env->yieldContent('social_image', asset('images/apple-touch-icon.png')));
        $openGraphType = trim($__env->yieldContent('og_type', 'website'));
        $fullSeoTitle = $seoTitle !== '' ? $seoTitle.' | '.config('app.name') : config('app.name');
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="{{ $shouldNoIndex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large' }}">
    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:locale" content="nl_NL">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="{{ $openGraphType }}">
    <meta property="og:title" content="{{ $fullSeoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullSeoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <link rel="icon" type="image/webp" sizes="64x64" href="{{ asset('images/favicon-v2.webp') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    @yield('meta')
    <title>{{ $fullSeoTitle }}</title>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'OnlineStore',
            'name' => \App\Models\Setting::get('company_name', 'Kachelvloeistof.nl') ?: 'Kachelvloeistof.nl',
            'url' => url('/'),
            'email' => \App\Models\Setting::get('company_email', 'info@kachelvloeistof.nl'),
            'telephone' => \App\Models\Setting::get('company_phone', ''),
            'address' => \App\Models\Setting::get('company_address', ''),
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="turbo-site bg-gray-50 text-gray-900 min-h-screen flex flex-col antialiased">

@include('themes.default.components.nav')

<main class="flex-1">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        @yield('content')
    </div>
</main>

@include('themes.default.components.footer')

<div
    x-data
    x-show="$store.toast.show"
    x-transition
    x-init="@if(session()->has('toast')) $store.toast.open({{ Illuminate\Support\Js::from(session('toast')) }}) @endif"
    class="turbo-toast fixed left-3 right-3 top-3 z-[13000] flex items-center gap-3 rounded-xl border border-turbo-gold/40 bg-turbo-navy px-4 py-3 text-sm font-semibold text-white shadow-2xl sm:left-auto sm:right-6 sm:top-6 sm:max-w-md sm:px-6"
    role="status"
    aria-live="polite"
    style="display: none;"
>
    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-turbo-gold text-turbo-navy" aria-hidden="true">✓</span>
    <span x-text="$store.toast.message"></span>
</div>

<script>
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!form.action || !form.action.includes('/winkelmand/toevoegen/')) return;

    e.preventDefault();

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(new FormData(form)),
    })
    .then(r => r.json())
    .then(data => {
        window.Alpine.store('toast').open(data.message);
        window.dispatchEvent(new CustomEvent('cart-updated', { detail: data.count }));
    })
    .catch(() => {
        window.Alpine.store('toast').open('Product toegevoegd aan winkelmand');
    });
});
</script>


@include('components.whatsapp-fab')


</body>
</html>
