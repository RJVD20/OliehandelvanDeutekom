@extends('admin.layouts.app')

@section('title', 'Bestelling #' . $order->id)

@section('content')

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold">Bestelling #{{ $order->id }}</h1>
        <p class="text-sm text-gray-500 mt-1">Aangemaakt op {{ $order->created_at->format('d-m-Y') }}</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide
            @if($order->status === 'pending') bg-yellow-100 text-yellow-700
            @elseif($order->status === 'shipped') bg-blue-100 text-blue-700
            @elseif($order->status === 'completed') bg-green-100 text-green-700
            @endif
        ">
            {{ ucfirst($order->status) }}
        </span>
        <span class="text-sm font-semibold text-green-700">€ {{ number_format($order->total, 2, ',', '.') }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Betaling -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-2 lg:col-span-1">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold">Betaling</h2>
            <span class="text-xs text-gray-500">Status</span>
        </div>
        @php $payment = $order->latestPayment; @endphp
        @if($payment)
            <p class="text-sm flex items-center justify-between"><span class="text-gray-600">Status</span><strong>{{ ucfirst($payment->status->value) }}</strong></p>
            <p class="text-sm"><strong>Vervaldatum:</strong> {{ optional($payment->due_date)->format('d-m-Y') }}</p>
            <p class="text-sm"><strong>Laatste herinnering:</strong> {{ optional($payment->last_reminder_at)->format('d-m-Y H:i') ?? '–' }}</p>
            <p class="text-sm"><strong>Aantal herinneringen:</strong> {{ $payment->reminder_count }}</p>
            <p class="text-sm font-semibold text-green-700">€ {{ number_format($payment->amount, 2, ',', '.') }}</p>
            @if($payment->pay_link)
                <a href="{{ $payment->pay_link }}" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white text-sm font-semibold mt-2" target="_blank" rel="noopener">Open betaallink</a>
            @endif
        @else
            <p class="text-sm text-gray-600">Geen betaling geregistreerd.</p>
        @endif
    </div>

    <!-- Klantgegevens -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-2">
        <h2 class="font-semibold">Klant</h2>

        <p class="text-sm"><span class="text-gray-600">Naam</span><br><strong>{{ $order->name }}</strong></p>
        <p class="text-sm"><span class="text-gray-600">Email</span><br><strong>{{ $order->email }}</strong></p>

        <p class="mt-2 text-sm leading-relaxed">
            <strong>Adres:</strong><br>
            {{ $order->address }}<br>
            {{ $order->postcode }} {{ $order->city }}<br>
            {{ $order->province ?? 'Provincie onbekend' }}
        </p>
    </div>

    <!-- Bestelling -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm lg:col-span-2">
        <h2 class="font-semibold">Producten</h2>

        <div class="hidden md:block overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-gray-500">
                        <th class="text-left p-2">Product</th>
                        <th class="p-2">Aantal</th>
                        <th class="p-2">Prijs</th>
                        <th class="p-2">Subtotaal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b">
                            <td class="p-2">{{ $item->product_name }}</td>
                            <td class="p-2 text-center">{{ $item->quantity }}</td>
                            <td class="p-2 text-right">
                                € {{ number_format($item->price, 2, ',', '.') }}
                            </td>
                            <td class="p-2 text-right">
                                € {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            @foreach($order->items as $item)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold leading-tight">{{ $item->product_name }}</p>
                        <p class="text-sm text-gray-600">x{{ $item->quantity }}</p>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm">
                        <span class="text-gray-600">€ {{ number_format($item->price, 2, ',', '.') }} p/st</span>
                        <span class="font-semibold text-green-700">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-right mt-4 font-bold text-green-700 text-lg">
            Totaal: € {{ number_format($order->total, 2, ',', '.') }}
        </div>
    </div>

</div>

<!-- Routeplanning en acties -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-gradient-to-br from-white via-white to-emerald-50/40 p-6 rounded-2xl border border-emerald-100/60 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 11l19-7-7 19-2-7-7-2z" />
                    </svg>
                </span>
                <h2 class="font-semibold">Route plannen</h2>
            </div>
            @if($order->route_date)
                <span class="text-xs text-gray-500">{{ $order->route_date->format('d-m-Y') }}</span>
            @endif
        </div>

        <form id="order-plan-form" method="POST" action="{{ route('admin.orders.plan', $order) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="space-y-1">
                <label class="block text-sm text-gray-600">Provincie</label>
                <select name="province" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
                    <option value="">— kies provincie —</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" @selected($order->province === $province)>
                            {{ $province }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-sm text-gray-600">Route datum</label>
                    <input
                        type="date"
                        name="route_date"
                        value="{{ optional($order->route_date)->format('Y-m-d') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
                    >
                </div>

                <div class="space-y-1">
                    <label class="block text-sm text-gray-600">Volgorde (nummer)</label>
                    <input
                        type="number"
                        name="route_sequence"
                        min="1"
                        value="{{ $order->route_sequence }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
                    >
                </div>
            </div>

        </form>
        <div class="mt-2 flex flex-col sm:flex-row gap-3">
            <button
                form="order-plan-form"
                class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700"
                type="submit"
            >
                Opslaan
            </button>
            @if($order->route_date)
                <form method="POST" action="{{ route('admin.routes.remove', $order) }}" class="w-full sm:w-auto">
                    @csrf
                    @method('PATCH')
                    <button class="w-full px-4 py-2 border rounded-lg text-center text-sm font-semibold text-gray-800" type="submit">
                        Reset route
                    </button>
                </form>
                @php
                    $routeParams = ['route_date' => $order->route_date->format('Y-m-d')];
                    if ($order->province) {
                        $routeParams['province'] = $order->province;
                    }
                @endphp
                <a
                    href="{{ route('admin.routes.index', $routeParams) }}"
                    class="w-full sm:w-auto px-4 py-2 border border-emerald-600 text-emerald-700 rounded-lg text-center text-sm font-semibold hover:bg-emerald-50"
                >
                    Ga naar route
                </a>
            @endif
        </div>
    </div>

</div>

@endsection
