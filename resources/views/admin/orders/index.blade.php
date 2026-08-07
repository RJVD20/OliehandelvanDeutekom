@extends('admin.layouts.app')

@section('title', 'Bestellingen')

@section('content')
@php
    $activeTab = $filters['tab'] ?? 'all';
    $tabs = [
        'all' => 'Alle',
        'new' => 'Nieuw',
        'paid' => 'Betaald',
        'unpaid' => 'Onbetaald',
        'planned' => 'Ingepland',
        'shipped' => 'Verzonden',
        'completed' => 'Afgerond',
        'cancelled' => 'Geannuleerd',
    ];
    $statusPresentation = [
        'pending' => ['Nieuw', 'bg-amber-100 text-amber-800'],
        'shipped' => ['Verzonden', 'bg-blue-100 text-blue-800'],
        'completed' => ['Afgerond', 'bg-emerald-100 text-emerald-800'],
        'cancelled' => ['Geannuleerd', 'bg-red-100 text-red-700'],
        'awaiting_payment' => ['Wacht op betaling', 'bg-gray-100 text-gray-700'],
    ];
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-green-700">Webshopbeheer</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Bestellingen</h1>
        <p class="mt-1 text-sm text-gray-500">Bekijk en beheer alle webshop- en handmatige bestellingen.</p>
    </div>
    <a href="{{ route('admin.orders.create') }}" class="rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
        + Handmatige bestelling
    </a>
</div>

<section class="mb-6 grid grid-cols-2 gap-3 xl:grid-cols-4" aria-label="Bestellingstatistieken">
    @foreach([
        ['Nieuwe bestellingen', $stats['new'], 'bg-amber-50 text-amber-700', 'N'],
        ['Wachten op betaling', $stats['awaiting_payment'], 'bg-orange-50 text-orange-700', '€'],
        ['Klaar om te plannen', $stats['ready_to_plan'], 'bg-blue-50 text-blue-700', 'R'],
        ['Verzonden vandaag', $stats['shipped_today'], 'bg-emerald-50 text-emerald-700', '✓'],
    ] as [$label, $value, $colors, $icon])
        <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-medium text-gray-500 sm:text-sm">{{ $label }}</p>
                    <strong class="mt-2 block text-2xl text-gray-900 sm:text-3xl">{{ $value }}</strong>
                </div>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-sm font-bold {{ $colors }}">{{ $icon }}</span>
            </div>
        </article>
    @endforeach
</section>

<nav class="-mx-1 mb-4 flex gap-2 overflow-x-auto px-1 pb-2" aria-label="Bestellingstatus">
    @foreach($tabs as $value => $label)
        <a
            href="{{ route('admin.orders.index', array_filter([...request()->except('page', 'tab'), 'tab' => $value !== 'all' ? $value : null])) }}"
            class="whitespace-nowrap rounded-full border px-4 py-2 text-sm font-semibold transition {{ $activeTab === $value ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}"
        >
            {{ $label }}
        </a>
    @endforeach
</nav>

<form method="GET" action="{{ route('admin.orders.index') }}" class="mb-6 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
    @if($activeTab !== 'all')
        <input type="hidden" name="tab" value="{{ $activeTab }}">
    @endif
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(15rem,2fr)_repeat(4,minmax(9rem,1fr))_auto]">
        <label class="relative">
            <span class="sr-only">Bestelling zoeken</span>
            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>
            </svg>
            <input
                type="search"
                name="search"
                value="{{ $filters['search'] ?? '' }}"
                placeholder="Ordernummer, naam, e-mail of postcode"
                class="w-full rounded-xl border-gray-200 py-2.5 pl-10 text-sm"
            >
        </label>
        <input type="date" name="order_date" value="{{ $filters['order_date'] ?? '' }}" class="rounded-xl border-gray-200 text-sm" aria-label="Besteldatum">
        <select name="province" class="rounded-xl border-gray-200 text-sm" aria-label="Provincie">
            <option value="">Alle provincies</option>
            @foreach($provinces as $province)
                <option value="{{ $province }}" @selected(($filters['province'] ?? '') === $province)>{{ $province }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="rounded-xl border-gray-200 text-sm" aria-label="Betaalstatus">
            <option value="">Alle betalingen</option>
            <option value="paid" @selected(($filters['payment_status'] ?? '') === 'paid')>Betaald</option>
            <option value="open" @selected(($filters['payment_status'] ?? '') === 'open')>Openstaand</option>
            <option value="failed" @selected(($filters['payment_status'] ?? '') === 'failed')>Betaling mislukt</option>
            <option value="expired" @selected(($filters['payment_status'] ?? '') === 'expired')>Verlopen</option>
            <option value="cancelled" @selected(($filters['payment_status'] ?? '') === 'cancelled')>Geannuleerd</option>
        </select>
        <select name="fulfillment_method" class="rounded-xl border-gray-200 text-sm" aria-label="Ontvangstmethode">
            <option value="">Bezorgen en afhalen</option>
            <option value="delivery" @selected(($filters['fulfillment_method'] ?? '') === 'delivery')>Thuisbezorgen</option>
            <option value="pickup" @selected(($filters['fulfillment_method'] ?? '') === 'pickup')>Afhalen</option>
        </select>
        <button class="rounded-xl bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">Filteren</button>
    </div>
    @if(request()->hasAny(['search', 'order_date', 'province', 'payment_status', 'fulfillment_method']))
        <div class="mt-3">
            <a href="{{ route('admin.orders.index', $activeTab !== 'all' ? ['tab' => $activeTab] : []) }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">× Filters wissen</a>
        </div>
    @endif
</form>

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 sm:px-5">
        <h2 class="font-semibold text-gray-900">{{ $orders->total() }} {{ $orders->total() === 1 ? 'bestelling' : 'bestellingen' }}</h2>
        <a href="{{ route('admin.routes.smart') }}" class="text-sm font-semibold text-green-700 hover:text-green-800">Slim route plannen →</a>
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3">Bestelling</th>
                    <th class="px-5 py-3">Klant</th>
                    <th class="px-5 py-3">Ontvangst</th>
                    <th class="px-5 py-3">Betaling</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Bedrag</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    @php
                        [$statusLabel, $statusClasses] = $statusPresentation[$order->status->value] ?? [ucfirst($order->status->value), 'bg-gray-100 text-gray-700'];
                        $payment = $order->latestPayment;
                    @endphp
                    <tr class="transition hover:bg-gray-50">
                        <td class="whitespace-nowrap px-5 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-gray-900 hover:text-green-700">#{{ $order->id }}</a>
                            <p class="mt-0.5 text-xs text-gray-400">{{ $order->created_at->format('d-m-Y H:i') }}</p>
                            @if($order->source === 'manual')
                                <span class="mt-1 inline-flex rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-700">Handmatig</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <strong class="block max-w-[14rem] truncate text-gray-900">{{ $order->name }}</strong>
                            <span class="block max-w-[14rem] truncate text-xs text-gray-500">{{ $order->email ?: $order->postcode }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <strong class="block text-gray-700">{{ $order->fulfillment_method === 'pickup' ? 'Afhalen' : 'Bezorgen' }}</strong>
                            <span class="text-xs text-gray-500">
                                @if($order->fulfillment_method === 'pickup')
                                    {{ $order->pickup_location_name ?: 'Depot nog onbekend' }}
                                @elseif($order->delivery_route_id)
                                    Ingepland op {{ $order->route_date?->format('d-m-Y') }}
                                @else
                                    {{ $order->postcode }} {{ $order->city }}
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($payment)
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $payment->status->value === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($payment->status->value === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $payment->isCash() ? ($payment->isCashPending() ? 'Cash open' : 'Cash ontvangen') : $payment->statusLabel() }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Niet geregistreerd</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <strong class="text-gray-900">€ {{ number_format($order->total, 2, ',', '.') }}</strong>
                            <span class="block text-xs text-gray-400">{{ (int) $order->item_quantity }} artikelen</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:border-green-600 hover:text-green-700">
                                Bekijken →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-500">Geen bestellingen gevonden voor deze selectie.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="divide-y divide-gray-100 lg:hidden">
        @forelse($orders as $order)
            @php
                [$statusLabel, $statusClasses] = $statusPresentation[$order->status->value] ?? [ucfirst($order->status->value), 'bg-gray-100 text-gray-700'];
                $payment = $order->latestPayment;
            @endphp
            <article class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400">#{{ $order->id }} · {{ $order->created_at->format('d-m-Y H:i') }}</p>
                        <h3 class="mt-1 truncate font-semibold text-gray-900">{{ $order->name }}</h3>
                        <p class="truncate text-sm text-gray-500">{{ $order->email ?: $order->postcode.' '.$order->city }}</p>
                    </div>
                    <strong class="whitespace-nowrap text-green-700">€ {{ number_format($order->total, 2, ',', '.') }}</strong>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">{{ $order->fulfillment_method === 'pickup' ? 'Afhalen' : 'Bezorgen' }}</span>
                    @if($payment)
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $payment->status->value === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $payment->isCash() ? ($payment->isCashPending() ? 'Cash open' : 'Cash ontvangen') : $payment->statusLabel() }}</span>
                    @endif
                </div>
                <a href="{{ route('admin.orders.show', $order) }}" class="mt-4 flex w-full items-center justify-center rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700">
                    Bestelling bekijken →
                </a>
            </article>
        @empty
            <div class="p-10 text-center text-sm text-gray-500">Geen bestellingen gevonden voor deze selectie.</div>
        @endforelse
    </div>
</section>

<div class="mt-6">{{ $orders->links() }}</div>
@endsection
