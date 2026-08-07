@extends('admin.layouts.app')

@section('title', 'Bestelling #' . $order->id)

@section('content')
@php
    $adminStatusPresentation = [
        'pending' => ['Nieuw', 'bg-amber-100 text-amber-800'],
        'shipped' => ['Verzonden', 'bg-blue-100 text-blue-800'],
        'completed' => ['Afgerond', 'bg-emerald-100 text-emerald-800'],
        'cancelled' => ['Geannuleerd', 'bg-red-100 text-red-700'],
    ];
    [$adminStatusLabel, $adminStatusClasses] = $adminStatusPresentation[$order->status->value]
        ?? [ucfirst(str_replace('_', ' ', $order->status->value)), 'bg-gray-100 text-gray-700'];
@endphp

<div class="admin-order-header mb-6 flex flex-wrap items-center justify-between gap-5">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="mb-2 inline-flex text-sm font-semibold text-gray-500 hover:text-gray-800">← Terug naar bestellingen</a>
        <div class="flex items-center gap-3">
            <span class="admin-order-header__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 3h10v18l-2.5-1.7L12 21l-2.5-1.7L7 21V3Z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 8h4m-4 4h4" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <h1 class="text-2xl font-bold">Bestelling #{{ $order->id }}</h1>
        </div>
        <p class="text-sm text-gray-500 mt-1">
            Aangemaakt op {{ $order->created_at->format('d-m-Y') }}
            @if($order->source === 'manual')
                <span class="ml-2 rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-700">Handmatig</span>
            @endif
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $adminStatusClasses }}">
            {{ $adminStatusLabel }}
        </span>
        <span class="admin-order-header__total">€ {{ number_format($order->total, 2, ',', '.') }}</span>
        @if($order->status === \App\Enums\OrderStatus::PENDING)
            <form method="POST" action="{{ route('admin.orders.ship', $order) }}" onsubmit="return confirm('Bestelling als verzonden markeren en de verzendmail versturen?')">
                @csrf
                <button type="submit" class="rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    Verzending mailen
                </button>
            </form>
        @endif
        @if(in_array($order->status, [\App\Enums\OrderStatus::PENDING, \App\Enums\OrderStatus::SHIPPED], true))
            <form method="POST" action="{{ route('admin.orders.complete', $order) }}" onsubmit="return confirm('Weet je zeker dat je bestelling #{{ $order->id }} als afgerond wilt markeren?')">
                @csrf
                <button type="submit" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:border-emerald-300 hover:bg-emerald-100">
                    Bestelling afronden
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-8 2xl:grid-cols-12">

    <!-- Betaling -->
    <div class="admin-order-panel bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-2 lg:col-span-4 2xl:col-span-3">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold">Betaling</h2>
            <span class="text-xs text-gray-500">Status</span>
        </div>
        @php $payment = $order->latestPayment; @endphp
        @if($payment)
            @if($payment->isCash())
                <div class="rounded-xl border {{ $payment->isCashPending() ? 'border-amber-300 bg-amber-50 text-amber-900' : 'border-emerald-300 bg-emerald-50 text-emerald-800' }} p-4">
                    <p class="text-xs font-bold uppercase tracking-wide">Contante bestelling</p>
                    <p class="mt-1 text-lg font-bold">€ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                    <p class="mt-1 text-xs">{{ $payment->isCashPending() ? 'Nog niet als ontvangen afgevinkt.' : 'Ontvangen op '.$payment->paid_at?->format('d-m-Y H:i') }}</p>
                    @if($payment->isCashPending())
                        <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" class="mt-3" onsubmit="return confirm('Bevestig dat € {{ number_format($payment->amount, 2, ',', '.') }} contant is ontvangen.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Contant ontvangen afvinken</button>
                        </form>
                    @endif
                </div>
            @endif
            <p class="text-sm flex items-center justify-between"><span class="text-gray-600">Status</span><strong>{{ ucfirst($payment->status->value) }}</strong></p>
            <p class="text-sm flex items-center justify-between"><span class="text-gray-600">Betaalwijze</span><strong>{{ $payment->handlingLabel() }}</strong></p>
            <p class="text-sm"><strong>Vervaldatum:</strong> {{ optional($payment->due_date)->format('d-m-Y') }}</p>
            <p class="text-sm font-semibold text-green-700">€ {{ number_format($payment->amount, 2, ',', '.') }}</p>
            @if($payment->pay_link)
                <a href="{{ $payment->pay_link }}" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white text-sm font-semibold mt-2" target="_blank" rel="noopener">Open betaallink</a>
            @endif
            @if($payment->canSendManualPaymentRequest())
                <form method="POST" action="{{ route('admin.payments.send-request', $payment) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded bg-turbo-gold px-4 py-2 text-sm font-semibold text-turbo-navy">
                        Betaalverzoek versturen
                    </button>
                </form>
            @endif
            @if($payment->events->isNotEmpty())
                @php
                    $paymentEventLabels = [
                        'created' => 'Betaling aangemaakt',
                        'webhook' => 'Status ontvangen van betaalprovider',
                        'status_changed' => 'Betaalstatus gewijzigd',
                        'manual_payment_request' => 'Betaalverzoek verstuurd',
                        'admin_override' => 'Handmatig als betaald gemarkeerd',
                        'cash_received' => 'Contante betaling ontvangen',
                        'expired' => 'Betaling verlopen',
                    ];
                @endphp
                <details class="mt-4 border-t border-gray-100 pt-3">
                    <summary class="cursor-pointer text-xs font-semibold text-blue-700">Betaalhistorie ({{ $payment->events->count() }})</summary>
                    <ol class="mt-3 space-y-3 border-l border-gray-200 pl-4">
                        @foreach($payment->events->sortByDesc('created_at') as $event)
                            <li class="relative">
                                <span class="absolute -left-[1.18rem] top-1 h-2 w-2 rounded-full bg-blue-600 ring-2 ring-white"></span>
                                <strong class="block text-xs text-gray-800">{{ $paymentEventLabels[$event->type] ?? str($event->type)->replace('_', ' ')->ucfirst() }}</strong>
                                <span class="block text-[11px] text-gray-500">
                                    {{ $event->created_at->format('d-m-Y H:i') }}
                                    @if($event->actor)
                                        · {{ $event->actor->name }}
                                    @elseif($event->source)
                                        · {{ ucfirst($event->source) }}
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ol>
                </details>
            @endif
        @else
            <p class="text-sm text-gray-600">Geen betaling geregistreerd.</p>
        @endif
    </div>

    <!-- Klantgegevens -->
    <div class="admin-order-panel bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-2 lg:col-span-4 2xl:col-span-3">
        <h2 class="font-semibold">Klant</h2>

        <p class="text-sm"><span class="text-gray-600">Naam</span><br><strong>{{ $order->name }}</strong></p>
        <p class="text-sm"><span class="text-gray-600">E-mail</span><br><strong>{{ $order->email ?: 'Niet opgegeven' }}</strong></p>
        <p class="text-sm">
            <span class="text-gray-600">Ontvangst</span><br>
            <strong>
                {{ $order->fulfillment_method === 'pickup' ? 'Afhalen bij depot' : ($order->delivery_service === 'express' ? 'Express Premium' : 'Thuisbezorgen') }}
            </strong>
            @if((float) $order->shipping_cost > 0)
                <br><span class="text-xs text-gray-500">Bezorgkosten: € {{ number_format($order->shipping_cost, 2, ',', '.') }}</span>
            @endif
        </p>
        @if($order->fulfillment_method === 'pickup' && $order->pickup_location_name)
            <p class="text-sm">
                <span class="text-gray-600">Gekozen depot</span><br>
                <strong>{{ $order->pickup_location_name }}</strong><br>
                {{ $order->pickup_location_address }}
                @if($order->pickup_location_opening)
                    <br><span class="text-gray-500">Openingstijden: {{ $order->pickup_location_opening }}</span>
                @endif
            </p>
        @endif
        @if($order->phone)
            <p class="text-sm"><span class="text-gray-600">Telefoon</span><br><strong><a href="tel:{{ $order->phone }}" class="text-blue-700">{{ $order->phone }}</a></strong></p>
        @endif

        <p class="mt-2 text-sm leading-relaxed">
            <strong>Adres:</strong><br>
            {{ $order->address }}<br>
            {{ $order->postcode }} {{ $order->city }}<br>
            {{ $order->province ?? 'Provincie onbekend' }}
        </p>
    </div>

    <!-- Bestelling -->
    <div class="admin-order-panel bg-white p-6 rounded-2xl border border-gray-100 shadow-sm lg:col-span-8 2xl:col-span-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold">Producten</h2>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">{{ $order->items->sum('quantity') }} artikelen</span>
        </div>
        @if($promotionName = $order->items->pluck('promotion_name')->filter()->first())
            <div class="mt-4 rounded-xl border border-turbo-gold/40 bg-turbo-gold/10 p-3 text-sm"><strong>Actiebundel:</strong> {{ $promotionName }}</div>
        @endif

        <div class="hidden md:block overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-gray-500">
                        <th class="w-14 p-2"><span class="sr-only">Afbeelding</span></th>
                        <th class="text-left p-2">Product</th>
                        <th class="p-2">Aantal</th>
                        <th class="p-2">Prijs</th>
                        <th class="p-2">Subtotaal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b">
                            <td class="p-2">
                                <div class="admin-order-product-image">
                                    @if($item->product?->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="" loading="lazy">
                                    @else
                                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 8h12l-1 12H7L6 8Zm3 0V6a3 3 0 0 1 6 0v2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @endif
                                </div>
                            </td>
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
                        <div class="flex items-center gap-3">
                            <div class="admin-order-product-image">
                                @if($item->product?->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="" loading="lazy">
                                @else
                                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 8h12l-1 12H7L6 8Zm3 0V6a3 3 0 0 1 6 0v2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @endif
                            </div>
                            <p class="font-semibold leading-tight">{{ $item->product_name }}</p>
                        </div>
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

<section class="admin-order-panel mt-6 rounded-2xl border border-amber-200 bg-amber-50/60 p-5 shadow-sm">
    <div class="flex items-start gap-3">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5"><path d="M7 4h10a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-5l-4 3v-3H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <div>
            <h2 class="font-semibold text-amber-950">Opmerking voor uitvoering</h2>
            <p class="mt-1 text-xs leading-5 text-amber-800">Deze opmerking verschijnt op de paklijst, in de routeplanning en in de chauffeursapp.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.orders.notes', $order) }}" class="mt-4">
        @csrf
        @method('PATCH')
        <label for="order-notes" class="sr-only">Opmerking voor uitvoering</label>
        <textarea id="order-notes" name="route_notes" rows="3" maxlength="2000" placeholder="Bijvoorbeeld: achterom leveren, klant vooraf bellen of verpakking controleren…" class="w-full rounded-xl border-amber-200 bg-white p-3 text-sm">{{ old('route_notes', $order->route_notes) }}</textarea>
        @error('route_notes')
            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
        <div class="mt-3 flex justify-end">
            <button type="submit" class="rounded-xl bg-turbo-navy px-4 py-2.5 text-sm font-semibold text-white hover:bg-turbo-dark">Opmerking opslaan</button>
        </div>
    </form>
</section>

<!-- Routeplanning en acties -->
<div class="mt-6">
    <div class="admin-order-panel bg-gradient-to-br from-white via-white to-emerald-50/40 p-6 rounded-2xl border border-emerald-100/60 shadow-sm">
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

        <form id="order-plan-form" method="POST" action="{{ route('admin.orders.plan', $order) }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @csrf
            @method('PATCH')

            <div class="space-y-1">
                <label class="block text-sm text-gray-600">Route</label>
                <select name="delivery_route_id" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
                    <option value="">— geen route —</option>
                    @foreach($deliveryRoutes as $route)
                        <option value="{{ $route->id }}" @selected($order->delivery_route_id === $route->id)>
                            {{ $route->name }} — {{ $route->route_date->format('d-m-Y') }}
                        </option>
                    @endforeach
                </select>
                <div class="text-xs text-gray-500 mt-1">Kies een route om datum/provincie automatisch te vullen.</div>
            </div>

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

            <div class="contents">
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
