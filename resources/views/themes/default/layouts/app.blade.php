<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <title>@yield('title', 'Webshop')</title>
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
