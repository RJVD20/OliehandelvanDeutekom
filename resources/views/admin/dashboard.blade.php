@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $statusLabels = [
        'pending' => ['Nieuw', 'bg-amber-100 text-amber-800'],
        'shipped' => ['Verzonden', 'bg-blue-100 text-blue-800'],
        'completed' => ['Afgerond', 'bg-emerald-100 text-emerald-800'],
        'cancelled' => ['Geannuleerd', 'bg-red-100 text-red-700'],
    ];
    $auditActions = [
        'created' => 'maakte aan',
        'updated' => 'wijzigde',
        'deleted' => 'verwijderde',
    ];
    $auditSubjects = [
        'product' => 'product',
        'cms' => 'CMS-inhoud',
        'location' => 'locatie',
        'payment' => 'betaling',
        'newsletter' => 'nieuwsbrief',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-green-700">Vandaag, {{ now()->translatedFormat('l j F') }}</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Goedemorgen, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">Dit speelt er momenteel in de webshop en bezorgplanning.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.create') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:border-gray-300">+ Handmatige bestelling</a>
        <a href="{{ route('admin.routes.smart') }}" class="rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">✨ Slim route plannen</a>
    </div>
</div>

@if($maintenanceEnabled)
    <section class="mb-6 flex flex-col gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <strong class="text-red-900">Onderhoudsmodus staat aan</strong>
            <p class="mt-0.5 text-sm text-red-700">Bezoekers zien momenteel de onderhoudspagina. Beheerders kunnen de site nog wel bekijken.</p>
        </div>
        <form method="POST" action="{{ route('admin.maintenance.toggle') }}">
            @csrf
            <button class="w-full rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-red-100 sm:w-auto">Onderhoudsmodus uitzetten</button>
        </form>
    </section>
@endif

<section class="mb-6 grid grid-cols-2 gap-3 xl:grid-cols-4" aria-label="Belangrijkste cijfers">
    @foreach([
        ['Bestellingen vandaag', $ordersToday, 'admin.orders.index', ['order_date' => today()->toDateString()], 'bg-blue-50 text-blue-700', 'B'],
        ['Omzet deze maand', '€ '.number_format($revenueThisMonth, 2, ',', '.'), 'admin.orders.index', ['tab' => 'paid'], 'bg-emerald-50 text-emerald-700', '€'],
        ['Open betalingen', $openPayments, 'admin.orders.index', ['tab' => 'unpaid'], 'bg-amber-50 text-amber-700', '!'],
        ['Nog in te plannen', $unplannedDeliveries, 'admin.routes.smart', [], 'bg-purple-50 text-purple-700', 'R'],
    ] as [$label, $value, $routeName, $parameters, $colors, $icon])
        <a href="{{ route($routeName, $parameters) }}" class="group rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-200 hover:shadow-md sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-medium text-gray-500 sm:text-sm">{{ $label }}</p>
                    <strong class="mt-2 block text-xl text-gray-900 sm:text-3xl">{{ $value }}</strong>
                </div>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-sm font-bold {{ $colors }}">{{ $icon }}</span>
            </div>
            <span class="mt-3 block text-xs font-semibold text-gray-400 group-hover:text-green-700">Bekijken →</span>
        </a>
    @endforeach
</section>

<div class="grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.65fr)]">
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
            <div>
                <h2 class="font-semibold text-gray-900">Recente bestellingen</h2>
                <p class="text-xs text-gray-500">De laatst binnengekomen webshop- en handmatige orders.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-green-700">Alle bekijken →</a>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($recentOrders as $order)
                @php
                    [$statusLabel, $statusClasses] = $statusLabels[$order->status->value] ?? [ucfirst($order->status->value), 'bg-gray-100 text-gray-700'];
                    $payment = $order->latestPayment;
                @endphp
                <a href="{{ route('admin.orders.show', $order) }}" class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 px-4 py-2.5 transition hover:bg-gray-50">
                    <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-gray-100 px-1.5 text-[11px] font-bold text-gray-700">#{{ $order->id }}</span>
                    <span class="min-w-0">
                        <span class="flex min-w-0 items-center gap-2">
                            <strong class="truncate text-sm text-gray-900">{{ $order->name }}</strong>
                            <span class="hidden shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold sm:inline-flex {{ $statusClasses }}">{{ $statusLabel }}</span>
                            @if($payment)
                                <span class="hidden shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold md:inline-flex {{ $payment->status->value === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $payment->statusLabel() }}</span>
                            @endif
                        </span>
                        <span class="block truncate text-[11px] text-gray-500">{{ $order->created_at->format('d-m H:i') }} · {{ $order->fulfillment_method === 'pickup' ? 'Afhalen' : $order->postcode.' '.$order->city }}</span>
                    </span>
                    <span class="text-right">
                        <strong class="block whitespace-nowrap text-sm text-green-700">€ {{ number_format($order->total, 2, ',', '.') }}</strong>
                        <span class="text-[10px] font-semibold text-gray-400">{{ (int) $order->item_quantity }} art.</span>
                    </span>
                </a>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">Er zijn nog geen bestellingen.</div>
            @endforelse
        </div>
    </section>

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Aankomende routes</h2>
                    <p class="text-xs text-gray-500">Eerstvolgende bezorgritten.</p>
                </div>
                <a href="{{ route('admin.routes.index') }}" class="text-xs font-semibold text-green-700">Beheren →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($upcomingRoutes as $route)
                    <a href="{{ route('admin.routes.index', ['route_date' => $route->route_date->toDateString(), 'route_id' => $route->id]) }}" class="flex items-center gap-3 p-4 hover:bg-gray-50">
                        <span class="inline-flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl bg-blue-50 text-blue-800">
                            <strong class="text-sm leading-none">{{ $route->route_date->format('d') }}</strong>
                            <span class="mt-0.5 text-[9px] uppercase">{{ $route->route_date->translatedFormat('M') }}</span>
                        </span>
                        <span class="min-w-0 flex-1">
                            <strong class="block truncate text-sm text-gray-900">{{ $route->name }}</strong>
                            <span class="text-xs text-gray-500">{{ $route->orders_count }} stops · {{ $route->admin?->name ?? 'Geen chauffeur' }}</span>
                        </span>
                        <span class="text-gray-300">›</span>
                    </a>
                @empty
                    <div class="p-6 text-center text-sm text-gray-500">Geen aankomende routes gepland.</div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-gray-900">Assortiment</h2>
                    <p class="text-xs text-gray-500">{{ $activeProducts }} actief · {{ $inactiveProducts }} inactief</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-green-700">Producten →</a>
            </div>
            <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-100">
                @php $productTotal = max(1, $activeProducts + $inactiveProducts); @endphp
                <div class="h-full rounded-full bg-green-600" style="width: {{ ($activeProducts / $productTotal) * 100 }}%"></div>
            </div>
        </section>
    </div>
</div>

<section class="mt-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
        <div>
            <h2 class="font-semibold text-gray-900">Recente wijzigingen</h2>
            <p class="text-xs text-gray-500">Laatste activiteiten van beheerders.</p>
        </div>
        <a href="{{ route('admin.audit.index') }}" class="text-sm font-semibold text-green-700">Volledig auditlog →</a>
    </div>
    <div class="grid divide-y divide-gray-100 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
        @forelse($recentAuditLogs as $log)
            <a href="{{ route('admin.audit.index', ['search' => $log->subject_label]) }}" class="flex items-start gap-3 p-4 hover:bg-gray-50">
                <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">{{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}</span>
                <span class="min-w-0">
                    <span class="block text-sm text-gray-700">
                        <strong>{{ $log->user?->name ?? 'Onbekend' }}</strong>
                        {{ $auditActions[$log->action] ?? $log->action }}
                        {{ $auditSubjects[$log->subject_type] ?? $log->subject_type }}
                    </span>
                    <span class="block truncate text-xs text-gray-500">{{ $log->subject_label }} · {{ $log->created_at->diffForHumans() }}</span>
                </span>
            </a>
        @empty
            <div class="p-6 text-sm text-gray-500">Nog geen auditactiviteiten geregistreerd.</div>
        @endforelse
    </div>
</section>

@if(!$maintenanceEnabled)
    <details class="mt-6 rounded-2xl border border-gray-100 bg-white shadow-sm">
        <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-700">Technisch beheer</summary>
        <div class="flex flex-col gap-3 border-t border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <strong class="text-sm text-gray-900">Onderhoudsmodus</strong>
                <p class="text-xs text-gray-500">Schakel de openbare website tijdelijk uit voor bezoekers.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.toggle') }}" onsubmit="return confirm('Onderhoudsmodus inschakelen voor alle bezoekers?')">
                @csrf
                <button class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50">Onderhoudsmodus aanzetten</button>
            </form>
        </div>
    </details>
@endif
@endsection
