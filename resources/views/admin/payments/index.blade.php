@extends('admin.layouts.app')

@section('title', 'Betalingen')

@section('content')
@php
    $activeTab = $filters['tab'] ?? 'all';
    $tabs = [
        'all' => 'Alles',
        'open' => 'Openstaand',
        'overdue' => 'Achterstallig',
        'soon' => 'Binnenkort',
        'paid' => 'Betaald',
        'failed' => 'Mislukt',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-green-700">Financieel overzicht</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Betalingen</h1>
        <p class="mt-1 text-sm text-gray-500">Openstaande bedragen, vervaldata en ontvangen betalingen op één plek.</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm">Naar bestellingen →</a>
</div>

@if(session('toast'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('toast') }}</div>
@endif

<section class="mb-6 grid grid-cols-2 gap-3 xl:grid-cols-4" aria-label="Betaalstatistieken">
    @foreach([
        ['Totaal openstaand', '€ '.number_format($stats['open_amount'], 2, ',', '.'), 'open', 'bg-amber-50 text-amber-700', '€'],
        ['Achterstallig', $stats['overdue_count'].' · € '.number_format($stats['overdue_amount'], 2, ',', '.'), 'overdue', 'bg-red-50 text-red-700', '!'],
        ['Vervalt binnen 3 dagen', $stats['due_soon_count'], 'soon', 'bg-orange-50 text-orange-700', '3d'],
        ['Betaald deze maand', '€ '.number_format($stats['paid_this_month'], 2, ',', '.'), 'paid', 'bg-emerald-50 text-emerald-700', '✓'],
    ] as [$label, $value, $tab, $colors, $icon])
        <a href="{{ route('admin.payments.index', ['tab' => $tab]) }}" class="group rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 sm:text-sm">{{ $label }}</p>
                    <strong class="mt-2 block truncate text-lg text-gray-900 sm:text-2xl">{{ $value }}</strong>
                </div>
                <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-1 text-xs font-bold {{ $colors }}">{{ $icon }}</span>
            </div>
        </a>
    @endforeach
</section>

<nav class="-mx-1 mb-4 flex gap-2 overflow-x-auto px-1 pb-2" aria-label="Betaalstatus">
    @foreach($tabs as $value => $label)
        <a
            href="{{ route('admin.payments.index', array_filter([...request()->except('page', 'tab'), 'tab' => $value !== 'all' ? $value : null])) }}"
            class="relative whitespace-nowrap rounded-full border px-4 py-2 text-sm font-semibold transition {{ $activeTab === $value ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}"
        >
            {{ $label }}
            @if($value === 'overdue' && $stats['overdue_count'] > 0)
                <span class="ml-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] text-white">{{ $stats['overdue_count'] }}</span>
            @endif
        </a>
    @endforeach
</nav>

<form method="GET" action="{{ route('admin.payments.index') }}" class="mb-6 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
    @if($activeTab !== 'all')
        <input type="hidden" name="tab" value="{{ $activeTab }}">
    @endif
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(16rem,2fr)_repeat(3,minmax(10rem,1fr))_auto]">
        <label class="relative">
            <span class="sr-only">Betaling zoeken</span>
            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>
            </svg>
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ordernummer, klant, e-mail of kenmerk" class="w-full rounded-xl border-gray-200 py-2.5 pl-10 text-sm">
        </label>
        <select name="handling" class="rounded-xl border-gray-200 text-sm">
            <option value="">Alle betaalwijzen</option>
            <option value="online" @selected(($filters['handling'] ?? '') === 'online')>Online betaling</option>
            <option value="payment_link" @selected(($filters['handling'] ?? '') === 'payment_link')>Betaallink</option>
            <option value="pay_on_delivery" @selected(($filters['handling'] ?? '') === 'pay_on_delivery')>Betalen bij levering</option>
            <option value="cash_on_delivery" @selected(($filters['handling'] ?? '') === 'cash_on_delivery')>Contant bij levering/afhalen</option>
            <option value="bank_transfer" @selected(($filters['handling'] ?? '') === 'bank_transfer')>Bankoverschrijving</option>
            <option value="manual" @selected(($filters['handling'] ?? '') === 'manual')>Handmatig</option>
        </select>
        <input type="date" name="due_after" value="{{ $filters['due_after'] ?? '' }}" class="rounded-xl border-gray-200 text-sm" aria-label="Vervalt vanaf">
        <input type="date" name="due_before" value="{{ $filters['due_before'] ?? '' }}" class="rounded-xl border-gray-200 text-sm" aria-label="Vervalt tot en met">
        <button class="rounded-xl bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">Filteren</button>
    </div>
    @if(request()->hasAny(['search', 'handling', 'due_after', 'due_before']))
        <div class="mt-3">
            <a href="{{ route('admin.payments.index', $activeTab !== 'all' ? ['tab' => $activeTab] : []) }}" class="text-sm font-medium text-gray-500 hover:text-gray-800">× Filters wissen</a>
        </div>
    @endif
</form>

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-3">
        <h2 class="font-semibold text-gray-900">{{ $payments->total() }} {{ $payments->total() === 1 ? 'betaling' : 'betalingen' }}</h2>
        <span class="text-xs text-gray-500">Acties worden in de betaalhistorie vastgelegd</span>
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3">Order en klant</th>
                    <th class="px-5 py-3">Betaalwijze</th>
                    <th class="px-5 py-3">Vervaldatum</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Bedrag</th>
                    <th class="px-5 py-3 text-right">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    @php
                        $isOpen = $payment->status === \App\Enums\PaymentStatus::OPEN;
                        $isDeliveryPayment = in_array($payment->handling(), ['pay_on_delivery', 'cash_on_delivery'], true);
                        $isOverdue = $isOpen && !$isDeliveryPayment && $payment->due_date?->lt(today());
                        $isDueSoon = $isOpen && !$isDeliveryPayment && !$isOverdue && $payment->due_date?->lte(today()->addDays(3));
                        $statusClasses = match($payment->status->value) {
                            'paid' => 'bg-emerald-100 text-emerald-700',
                            'open' => $isOverdue ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700',
                            'failed', 'expired' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.orders.show', $payment->order) }}" class="font-bold text-gray-900 hover:text-green-700">#{{ $payment->order_id }} · {{ $payment->order->name }}</a>
                            <span class="block max-w-[16rem] truncate text-xs text-gray-500">{{ $payment->order->email }}</span>
                            @if($payment->provider_payment_id)
                                <span class="block max-w-[16rem] truncate font-mono text-[10px] text-gray-400">{{ $payment->provider_payment_id }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <strong class="text-gray-700">{{ $payment->handlingLabel() }}</strong>
                            @if($payment->pay_link)
                                <a href="{{ $payment->pay_link }}" target="_blank" rel="noopener" class="mt-0.5 block text-xs font-semibold text-blue-700">Betaallink openen ↗</a>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($isDeliveryPayment)
                                <span class="text-gray-600">Bij levering</span>
                            @elseif($payment->status === \App\Enums\PaymentStatus::PAID)
                                <span class="text-gray-400">—</span>
                            @else
                                <strong class="{{ $isOverdue ? 'text-red-700' : ($isDueSoon ? 'text-orange-700' : 'text-gray-700') }}">{{ $payment->due_date?->format('d-m-Y') ?? 'Onbekend' }}</strong>
                                @if($isOverdue)
                                    <span class="block text-xs font-semibold text-red-600">{{ $payment->due_date->diffInDays(today()) }} dagen te laat</span>
                                @elseif($isDueSoon)
                                    <span class="block text-xs font-semibold text-orange-600">Binnenkort</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $isOverdue ? 'Achterstallig' : $payment->statusLabel() }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-right"><strong class="text-gray-900">€ {{ number_format($payment->amount, 2, ',', '.') }}</strong></td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700">Bekijken</a>
                                @if($payment->canSendManualPaymentRequest())
                                    <form method="POST" action="{{ route('admin.payments.send-request', $payment) }}">
                                        @csrf
                                        <button class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white">Betaalverzoek</button>
                                    </form>
                                @endif
                                @if($isOpen)
                                    <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" onsubmit="return confirm('Weet je zeker dat je deze betaling van € {{ number_format($payment->amount, 2, ',', '.') }} handmatig als betaald wilt markeren?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg border border-green-200 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-50">Markeer betaald</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">Geen betalingen gevonden voor deze selectie.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="divide-y divide-gray-100 lg:hidden">
        @forelse($payments as $payment)
            @php
                $isOpen = $payment->status === \App\Enums\PaymentStatus::OPEN;
                $isDeliveryPayment = in_array($payment->handling(), ['pay_on_delivery', 'cash_on_delivery'], true);
                $isOverdue = $isOpen && !$isDeliveryPayment && $payment->due_date?->lt(today());
            @endphp
            <article class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <a href="{{ route('admin.orders.show', $payment->order) }}" class="font-semibold text-gray-900">#{{ $payment->order_id }} {{ $payment->order->name }}</a>
                        <p class="truncate text-xs text-gray-500">{{ $payment->handlingLabel() }}</p>
                    </div>
                    <strong class="whitespace-nowrap text-green-700">€ {{ number_format($payment->amount, 2, ',', '.') }}</strong>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full px-2.5 py-1 font-semibold {{ $payment->status->value === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($isOverdue ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ $isOverdue ? 'Achterstallig' : $payment->statusLabel() }}</span>
                    <span class="text-gray-500">{{ $isDeliveryPayment ? 'Bij levering' : 'Vervalt: '.($payment->due_date?->format('d-m-Y') ?? 'onbekend') }}</span>
                </div>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    <a href="{{ route('admin.orders.show', $payment->order) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-700">Bestelling bekijken</a>
                    @if($payment->canSendManualPaymentRequest())
                        <form method="POST" action="{{ route('admin.payments.send-request', $payment) }}">@csrf<button class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Betaalverzoek versturen</button></form>
                    @endif
                    @if($isOpen)
                        <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" onsubmit="return confirm('Deze betaling handmatig als betaald markeren?')">
                            @csrf @method('PATCH')
                            <button class="w-full rounded-lg border border-green-200 px-3 py-2 text-sm font-semibold text-green-700">Markeer betaald</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="p-10 text-center text-sm text-gray-500">Geen betalingen gevonden voor deze selectie.</div>
        @endforelse
    </div>
</section>

<div class="mt-6">{{ $payments->links() }}</div>
@endsection
