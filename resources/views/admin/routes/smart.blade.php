@extends('admin.layouts.app')

@section('title', 'Slim route plannen')

@push('head')
@if($proposal)
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.css" rel="stylesheet">
@endif
<style>
    .smart-route-marker {
        display: flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        border-radius: 9999px;
        background: #d9a42e;
        color: #03182b;
        font-size: 12px;
        font-weight: 800;
        box-shadow: 0 2px 8px rgb(0 0 0 / 25%);
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-turbo-gold">Routeassistent</p>
        <h1 class="mt-1 text-2xl font-bold">Slim route plannen</h1>
        <p class="mt-1 text-sm text-gray-500">
            @if($proposal)
                Stap 2 van 2 — controleer de voorgestelde volgorde voordat je bevestigt.
            @else
                Stap 1 van 2 — kies de bestellingen; er wordt nog niets definitief opgeslagen.
            @endif
        </p>
    </div>
    <a href="{{ route('admin.routes.index') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">
        Naar routeoverzicht
    </a>
</div>

@if($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold">Het routevoorstel kon niet worden gemaakt:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(!$proposal)
    <form
        method="POST"
        action="{{ route('admin.routes.smart.preview') }}"
        class="space-y-6"
        x-data="{ selected: @json(collect(old('order_ids', []))->map(fn ($id) => (string) $id)->values()), max: 25 }"
    >
        @csrf

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold">Routegegevens</h2>
                    <p class="text-xs text-gray-500">Je kunt dit in het voorstel nog controleren.</p>
                </div>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700" x-text="`${selected.length} geselecteerd`"></span>
            </div>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <label class="space-y-1 text-sm">
                    <span class="font-medium text-gray-700">Bezorgdatum *</span>
                    <input type="date" name="route_date" value="{{ old('route_date', $routeData['route_date']) }}" min="{{ now()->toDateString() }}" required class="w-full rounded-xl border-gray-300">
                </label>
                <label class="space-y-1 text-sm">
                    <span class="font-medium text-gray-700">Routenaam *</span>
                    <input name="name" value="{{ old('name', $routeData['name']) }}" required class="w-full rounded-xl border-gray-300">
                </label>
                <label class="space-y-1 text-sm">
                    <span class="font-medium text-gray-700">Chauffeur</span>
                    <select name="admin_user_id" class="w-full rounded-xl border-gray-300">
                        <option value="">Later kiezen</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" @selected((string) old('admin_user_id') === (string) $admin->id)>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1 text-sm">
                    <span class="font-medium text-gray-700">Regio/provincie</span>
                    <select name="province" class="w-full rounded-xl border-gray-300">
                        <option value="">Meerdere regio’s</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" @selected(old('province') === $province)>{{ $province }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-5">
                <div>
                    <h2 class="font-semibold">Ongeplande bestellingen</h2>
                    <p class="text-sm text-gray-500">{{ $orders->count() }} beschikbaar · maximaal 25 stops per voorstel</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="selected = []" class="rounded-lg border px-3 py-2 text-xs font-semibold">Wis selectie</button>
                    <button
                        type="button"
                        @click="selected = @json($orders->take(25)->pluck('id')->map(fn ($id) => (string) $id)->values())"
                        class="rounded-lg border px-3 py-2 text-xs font-semibold"
                    >Selecteer eerste 25</button>
                </div>
            </div>

            @if($orders->isEmpty())
                <div class="p-8 text-center">
                    <p class="font-semibold text-gray-700">Alles is ingepland</p>
                    <p class="mt-1 text-sm text-gray-500">Er staan momenteel geen nieuwe bestellingen te wachten.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($orders->groupBy(fn ($order) => $order->province ?: 'Provincie onbekend') as $province => $provinceOrders)
                        <div class="bg-gray-50 px-5 py-2 text-xs font-bold uppercase tracking-wide text-gray-500">
                            {{ $province }} · {{ $provinceOrders->count() }} {{ Str::plural('stop', $provinceOrders->count()) }}
                        </div>
                        @foreach($provinceOrders as $order)
                            @php
                                $itemCount = $order->items->sum('quantity');
                                $payment = $order->latestPayment;
                                $isPaid = $payment?->status === \App\Enums\PaymentStatus::PAID;
                            @endphp
                            <label class="grid cursor-pointer gap-3 p-4 hover:bg-blue-50/40 sm:grid-cols-[auto_1fr_auto] sm:items-center">
                                <input
                                    type="checkbox"
                                    name="order_ids[]"
                                    value="{{ $order->id }}"
                                    x-model="selected"
                                    :disabled="selected.length >= max && !selected.includes('{{ $order->id }}')"
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600"
                                >
                                <span>
                                    <span class="flex flex-wrap items-center gap-2">
                                        <strong>#{{ $order->id }} {{ $order->name }}</strong>
                                        @if($order->source === 'manual')
                                            <span class="rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-700">Handmatig</span>
                                        @endif
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $isPaid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $isPaid ? 'Betaald' : 'Openstaand' }}
                                        </span>
                                    </span>
                                    <span class="mt-1 block text-sm text-gray-600">{{ $order->address }}, {{ $order->postcode }} {{ $order->city }}</span>
                                </span>
                                <span class="text-left sm:text-right">
                                    <strong class="block text-sm">€ {{ number_format($order->total, 2, ',', '.') }}</strong>
                                    <span class="text-xs text-gray-500">{{ $itemCount }} artikelen</span>
                                </span>
                            </label>
                        @endforeach
                    @endforeach
                </div>
            @endif
        </section>

        <div class="sticky bottom-4 flex justify-end">
            <button
                type="submit"
                :disabled="selected.length === 0"
                class="rounded-xl bg-green-600 px-6 py-3 font-semibold text-white shadow-lg hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                Maak slim routevoorstel →
            </button>
        </div>
    </form>
@else
    @if($proposal['warnings'])
        <div class="mb-6 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-900">
            <p class="font-semibold">Aandachtspunten</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($proposal['warnings'] as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $estimatedMinutes = ($proposal['travel_minutes'] ?? 0) + $proposal['stop_minutes'];
        $driver = $admins->firstWhere('id', (int) ($routeData['admin_user_id'] ?? 0));
    @endphp

    <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Route</span><strong class="mt-1 block">{{ $routeData['name'] }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Datum</span><strong class="mt-1 block">{{ \Carbon\Carbon::parse($routeData['route_date'])->format('d-m-Y') }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Chauffeur</span><strong class="mt-1 block">{{ $driver?->name ?? 'Nog niet gekozen' }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Schatting</span><strong class="mt-1 block">{{ $proposal['orders']->count() }} stops · {{ $estimatedMinutes ? floor($estimatedMinutes / 60).'u '.($estimatedMinutes % 60).'m' : 'onbekend' }}</strong></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(22rem,0.8fr)]">
        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 p-5">
                <h2 class="font-semibold">Voorgestelde volgorde</h2>
                <p class="text-sm text-gray-500">Sleep stops om de volgorde handmatig te wijzigen.</p>
            </div>

            <form method="POST" action="{{ route('admin.routes.smart.store') }}">
                @csrf
                <input type="hidden" name="route_date" value="{{ $routeData['route_date'] }}">
                <input type="hidden" name="name" value="{{ $routeData['name'] }}">
                <input type="hidden" name="province" value="{{ $routeData['province'] ?? '' }}">
                <input type="hidden" name="admin_user_id" value="{{ $routeData['admin_user_id'] ?? '' }}">

                <ol id="smart-route-list" class="divide-y divide-gray-100">
                    @foreach($proposal['orders'] as $order)
                        <li draggable="true" data-order-id="{{ $order->id }}" class="grid cursor-grab gap-3 p-4 active:cursor-grabbing sm:grid-cols-[auto_auto_1fr_auto] sm:items-center">
                            <span class="drag-handle text-xl text-gray-400" aria-hidden="true">☰</span>
                            <span data-sequence class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-turbo-gold font-bold text-turbo-navy">{{ $loop->iteration }}</span>
                            <span>
                                <strong class="block">#{{ $order->id }} {{ $order->name }}</strong>
                                <span class="block text-sm text-gray-600">{{ $order->address }}, {{ $order->postcode }} {{ $order->city }}</span>
                                @if($order->route_notes)
                                    <span class="mt-1 block text-xs text-purple-700">Notitie: {{ Str::limit($order->route_notes, 100) }}</span>
                                @endif
                            </span>
                            <span class="text-sm font-semibold">{{ $order->city }}</span>
                            <input type="hidden" name="order_ids[]" value="{{ $order->id }}">
                        </li>
                    @endforeach
                </ol>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 p-5">
                    <a href="{{ route('admin.routes.smart') }}" class="rounded-xl border px-4 py-3 text-sm font-semibold text-gray-700">← Opnieuw selecteren</a>
                    <button type="submit" class="rounded-xl bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700">
                        Route bevestigen
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm xl:sticky xl:top-6 xl:self-start">
            <div class="border-b border-gray-100 p-5">
                <h2 class="font-semibold">Controle op de kaart</h2>
                <p class="text-sm text-gray-500">De nummers volgen het automatische voorstel.</p>
            </div>
            <div id="smart-route-map" class="h-[28rem]">
                @if(!$mapboxToken)
                    <div class="p-5 text-sm text-gray-500">Mapbox-token ontbreekt; kaartweergave is niet beschikbaar.</div>
                @endif
            </div>
        </section>
    </div>
@endif
@endsection

@if($proposal)
@push('scripts')
@php
    $mapStops = $proposal['orders']->values()->map(function ($order, $index) use ($proposal) {
        $coordinate = $proposal['coordinates']->get($order->id);

        return [
            'sequence' => $index + 1,
            'id' => $order->id,
            'name' => $order->name,
            'address' => $order->address,
            'city' => $order->city,
            'lng' => $coordinate['lng'] ?? null,
            'lat' => $coordinate['lat'] ?? null,
        ];
    })->filter(fn ($stop) => $stop['lng'] !== null)->values();
@endphp
<script src="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.js"></script>
<script>
(() => {
    const list = document.getElementById('smart-route-list');
    if (!list) return;

    let dragged = null;
    const refresh = () => {
        [...list.children].forEach((item, index) => {
            item.querySelector('[data-sequence]').textContent = String(index + 1);
        });
    };

    list.addEventListener('dragstart', event => {
        dragged = event.target.closest('li');
        if (dragged) dragged.classList.add('opacity-50');
    });
    list.addEventListener('dragend', () => {
        if (dragged) dragged.classList.remove('opacity-50');
        dragged = null;
        refresh();
    });
    list.addEventListener('dragover', event => {
        event.preventDefault();
        const target = event.target.closest('li');
        if (!dragged || !target || target === dragged) return;
        const before = event.clientY < target.getBoundingClientRect().top + target.offsetHeight / 2;
        list.insertBefore(dragged, before ? target : target.nextSibling);
    });

    const token = @json($mapboxToken);
    const stops = @json($mapStops);

    if (!token || !stops.length || !window.mapboxgl) return;

    mapboxgl.accessToken = token;
    const map = new mapboxgl.Map({
        container: 'smart-route-map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [5.3, 52.1],
        zoom: 7
    });
    map.addControl(new mapboxgl.NavigationControl());

    map.on('load', () => {
        const bounds = new mapboxgl.LngLatBounds();
        stops.forEach(stop => {
            const marker = document.createElement('div');
            marker.className = 'smart-route-marker';
            marker.textContent = stop.sequence;
            new mapboxgl.Marker(marker)
                .setLngLat([stop.lng, stop.lat])
                .setPopup(new mapboxgl.Popup().setHTML(`<strong>#${stop.id} ${stop.name}</strong><br>${stop.address}<br>${stop.city}`))
                .addTo(map);
            bounds.extend([stop.lng, stop.lat]);
        });
        if (!bounds.isEmpty()) map.fitBounds(bounds, { padding: 45, maxZoom: 12 });
    });
})();
</script>
@endpush
@endif
