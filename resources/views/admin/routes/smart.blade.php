@extends('admin.layouts.app')

@section('title', 'Slim route plannen')

@push('head')
<link href="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.css" rel="stylesheet">
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
</div>

@if(session('toast'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
        {{ session('toast') }}
    </div>
@endif

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
        x-data='{
            selected: @json(collect(old('order_ids', []))->map(fn ($id) => (string) $id)->values()),
            orderProvinces: @json($orders->mapWithKeys(fn ($order) => [(string) $order->id => $order->province ?? ''])),
            province: @json(old('province', $routeData['province'] ?? '')),
            max: 25,
            selectedProvinces() {
                return [...new Set(
                    this.selected
                        .map(id => this.orderProvinces[id])
                        .filter(Boolean)
                )];
            },
            updateProvince() {
                const provinces = this.selectedProvinces();
                this.province = provinces.length === 1 ? provinces[0] : "";
            },
            provinceLabel() {
                if (this.province) {
                    return this.province;
                }

                return this.selectedProvinces().length > 1
                    ? "Meerdere provincies"
                    : "Automatisch na selectie";
            },
            provinceSelected(ids) {
                return ids.every(id => this.selected.includes(id));
            },
            canSelectProvince(ids) {
                return this.provinceSelected(ids)
                    || [...new Set([...this.selected, ...ids])].length <= this.max;
            },
            toggleProvince(ids) {
                if (this.provinceSelected(ids)) {
                    this.selected = this.selected.filter(id => !ids.includes(id));
                    return;
                }

                const next = [...new Set([...this.selected, ...ids])];
                if (next.length <= this.max) {
                    this.selected = next;
                }
            }
        }'
        x-init="updateProvince(); $watch('selected', () => updateProvince())"
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
                    <input type="hidden" name="province" :value="province">
                    <input
                        type="text"
                        :value="provinceLabel()"
                        readonly
                        class="w-full cursor-default rounded-xl border-gray-200 bg-gray-50 text-gray-600"
                    >
                </label>
            </div>
            <div class="mt-4 grid gap-4 border-t border-gray-100 pt-4 md:grid-cols-2">
                <label class="space-y-1 text-sm">
                    <span class="font-medium text-gray-700">Startlocatie</span>
                    <select name="start_location_id" class="w-full rounded-xl border-gray-300">
                        <option value="">Geen depot als startpunt</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @selected((string) old('start_location_id', $routeData['start_location_id'] ?? '') === (string) $location->id)>
                                {{ $location->name }} — {{ $location->street }}, {{ $location->postcode_city }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1 text-sm">
                    <span class="font-medium text-gray-700">Eindlocatie</span>
                    <select name="end_location_id" class="w-full rounded-xl border-gray-300">
                        <option value="">Geen depot als eindpunt</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @selected((string) old('end_location_id', $routeData['end_location_id'] ?? '') === (string) $location->id)>
                                {{ $location->name }} — {{ $location->street }}, {{ $location->postcode_city }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <p class="text-xs text-gray-500 md:col-span-2">
                    Depots worden gebruikt voor de routeberekening, maar niet als bezorgstop opgeslagen en zijn daardoor niet zichtbaar in de chauffeursapp.
                </p>
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
                        @php
                            $provinceOrderIds = $provinceOrders
                                ->pluck('id')
                                ->map(fn ($id) => (string) $id)
                                ->values();
                        @endphp
                        <div class="flex flex-wrap items-center justify-between gap-3 bg-gray-50 px-5 py-2">
                            <span class="text-xs font-bold uppercase tracking-wide text-gray-500">
                                {{ $province }} · {{ $provinceOrders->count() }} {{ Str::plural('stop', $provinceOrders->count()) }}
                            </span>
                            <label
                                class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-blue-700"
                                :class="{ 'cursor-not-allowed opacity-40': !canSelectProvince(@js($provinceOrderIds)) }"
                                title="Selecteer alle bestellingen uit {{ $province }}"
                            >
                                <input
                                    type="checkbox"
                                    :checked="provinceSelected(@js($provinceOrderIds))"
                                    :disabled="!canSelectProvince(@js($provinceOrderIds))"
                                    @change="toggleProvince(@js($provinceOrderIds))"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600"
                                >
                                Hele provincie
                            </label>
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

    <details class="mt-6 rounded-2xl border border-gray-100 bg-white shadow-sm">
        <summary class="flex cursor-pointer items-center justify-between gap-4 p-5">
            <span>
                <strong class="block">Route-optimalisatie</strong>
                <span class="mt-1 block text-sm text-gray-500">
                    {{ $googleUsage['used'] }} van {{ $googleUsage['limit'] }} Google-aanvragen gebruikt in {{ $googleUsage['period'] }}
                </span>
            </span>
            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $googleUsage['fallback_active'] ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                {{ $googleUsage['fallback_active'] ? 'Mapbox actief' : 'Google actief' }}
            </span>
        </summary>
        <form method="POST" action="{{ route('admin.routes.smart.settings') }}" class="grid gap-4 border-t border-gray-100 p-5 sm:grid-cols-[1fr_1.5fr_auto] sm:items-end">
            @csrf
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Maandlimiet Google</span>
                <input
                    type="number"
                    name="google_routes_monthly_limit"
                    min="1"
                    max="5000"
                    value="{{ old('google_routes_monthly_limit', $googleUsage['limit']) }}"
                    required
                    class="w-full rounded-xl border-gray-300"
                >
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Waarschuwingsmail</span>
                <input
                    type="email"
                    name="google_routes_alert_email"
                    value="{{ old('google_routes_alert_email', $googleUsage['alert_email']) }}"
                    required
                    class="w-full rounded-xl border-gray-300"
                >
            </label>
            <button type="submit" class="rounded-xl bg-turbo-blue px-5 py-3 text-sm font-semibold text-white">
                Instellingen opslaan
            </button>
        </form>
    </details>

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
        $startLocation = $locations->firstWhere('id', (int) ($routeData['start_location_id'] ?? 0));
        $endLocation = $locations->firstWhere('id', (int) ($routeData['end_location_id'] ?? 0));
    @endphp

    <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Route</span><strong class="mt-1 block">{{ $routeData['name'] }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Datum</span><strong class="mt-1 block">{{ \Carbon\Carbon::parse($routeData['route_date'])->format('d-m-Y') }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Chauffeur</span><strong class="mt-1 block">{{ $driver?->name ?? 'Nog niet gekozen' }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Startlocatie</span><strong class="mt-1 block">{{ $startLocation?->name ?? 'Geen depot gekozen' }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm"><span class="text-xs text-gray-500">Eindlocatie</span><strong class="mt-1 block">{{ $endLocation?->name ?? 'Geen depot gekozen' }}</strong></div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <span class="text-xs text-gray-500">Schatting</span>
            <strong class="mt-1 block">{{ $proposal['orders']->count() }} stops · {{ $estimatedMinutes ? floor($estimatedMinutes / 60).'u '.($estimatedMinutes % 60).'m' : 'onbekend' }}</strong>
            <span class="mt-1 block text-xs text-gray-500">Geoptimaliseerd met {{ ucfirst($proposal['optimization_provider'] ?? 'postcode') }}</span>
        </div>
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
                <input type="hidden" name="start_location_id" value="{{ $routeData['start_location_id'] ?? '' }}">
                <input type="hidden" name="end_location_id" value="{{ $routeData['end_location_id'] ?? '' }}">

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
                <p class="text-sm text-gray-500">De nummers en routelijn volgen direct jouw gekozen volgorde.</p>
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
    $depotMarkers = collect([
        $proposal['start_location'] && $proposal['start_coordinate'] ? [
            'kind' => 'Start',
            'name' => $proposal['start_location']->name,
            'address' => trim($proposal['start_location']->street.', '.$proposal['start_location']->postcode_city, ' ,'),
            'lng' => $proposal['start_coordinate']['lng'],
            'lat' => $proposal['start_coordinate']['lat'],
        ] : null,
        $proposal['end_location'] && $proposal['end_coordinate'] ? [
            'kind' => 'Eind',
            'name' => $proposal['end_location']->name,
            'address' => trim($proposal['end_location']->street.', '.$proposal['end_location']->postcode_city, ' ,'),
            'lng' => $proposal['end_coordinate']['lng'],
            'lat' => $proposal['end_coordinate']['lat'],
        ] : null,
    ])->filter()->values();
@endphp
<script src="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.js"></script>
<script>
(() => {
    const list = document.getElementById('smart-route-list');
    if (!list) return;

    let dragged = null;
    let refreshMap = () => {};
    const refresh = () => {
        [...list.children].forEach((item, index) => {
            item.querySelector('[data-sequence]').textContent = String(index + 1);
        });
        refreshMap();
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
        refresh();
    });

    const token = @json($mapboxToken);
    const stops = @json($mapStops);
    const depots = @json($depotMarkers);
    let currentGeometry = @json($proposal['route_geometry'] ?? null);

    if (!token || !stops.length || !window.mapboxgl) return;

    mapboxgl.accessToken = token;
    const map = new mapboxgl.Map({
        container: 'smart-route-map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [5.3, 52.1],
        zoom: 7
    });
    map.addControl(new mapboxgl.NavigationControl());

    class StyleToggleControl {
        onAdd(controlMap) {
            this.map = controlMap;
            this.satellite = false;
            this.container = document.createElement('div');
            this.container.className = 'mapboxgl-ctrl mapboxgl-ctrl-group';
            this.button = document.createElement('button');
            this.button.type = 'button';
            this.button.title = 'Schakel tussen kaart en satelliet';
            this.button.textContent = 'Satelliet';
            this.button.style.width = 'auto';
            this.button.style.padding = '0 10px';
            this.button.style.fontSize = '12px';
            this.button.style.fontWeight = '700';
            this.button.addEventListener('click', () => {
                this.satellite = !this.satellite;
                this.button.textContent = this.satellite ? 'Kaart' : 'Satelliet';
                this.map.setStyle(this.satellite
                    ? 'mapbox://styles/mapbox/satellite-streets-v12'
                    : 'mapbox://styles/mapbox/streets-v12'
                );
            });
            this.container.appendChild(this.button);
            return this.container;
        }

        onRemove() {
            this.container.remove();
            this.map = undefined;
        }
    }

    map.addControl(new StyleToggleControl(), 'top-left');

    const renderRouteLine = () => {
        if (!currentGeometry?.coordinates?.length || !map.isStyleLoaded()) return;

        if (!map.getSource('planned-route')) {
            map.addSource('planned-route', {
                type: 'geojson',
                data: {
                    type: 'Feature',
                    properties: {},
                    geometry: currentGeometry
                }
            });
            map.addLayer({
                id: 'planned-route-outline',
                type: 'line',
                source: 'planned-route',
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round'
                },
                paint: {
                    'line-color': '#ffffff',
                    'line-width': 8,
                    'line-opacity': 0.9
                }
            });
            map.addLayer({
                id: 'planned-route',
                type: 'line',
                source: 'planned-route',
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round'
                },
                paint: {
                    'line-color': '#0f766e',
                    'line-width': 5,
                    'line-opacity': 0.95
                }
            });
        } else {
            map.getSource('planned-route').setData({
                type: 'Feature',
                properties: {},
                geometry: currentGeometry
            });
        }
    };

    map.on('style.load', renderRouteLine);

    map.on('load', () => {
        const bounds = new mapboxgl.LngLatBounds();
        const markersByOrderId = new Map();
        const stopsByOrderId = new Map(stops.map(stop => [String(stop.id), stop]));
        let routeRequest = null;
        let routeTimer = null;
        renderRouteLine();

        stops.forEach(stop => {
            const marker = document.createElement('div');
            marker.className = 'smart-route-marker';
            marker.textContent = stop.sequence;
            new mapboxgl.Marker(marker)
                .setLngLat([stop.lng, stop.lat])
                .setPopup(new mapboxgl.Popup().setHTML(`<strong>#${stop.id} ${stop.name}</strong><br>${stop.address}<br>${stop.city}`))
                .addTo(map);
            markersByOrderId.set(String(stop.id), marker);
            bounds.extend([stop.lng, stop.lat]);
        });
        depots.forEach(depot => {
            const marker = document.createElement('div');
            marker.className = 'smart-route-marker';
            marker.style.background = depot.kind === 'Start' ? '#16a34a' : '#dc2626';
            marker.style.color = '#ffffff';
            marker.textContent = depot.kind === 'Start' ? 'S' : 'E';
            new mapboxgl.Marker(marker)
                .setLngLat([depot.lng, depot.lat])
                .setPopup(new mapboxgl.Popup().setHTML(`<strong>${depot.kind}: ${depot.name}</strong><br>${depot.address}`))
                .addTo(map);
            bounds.extend([depot.lng, depot.lat]);
        });
        if (!bounds.isEmpty()) map.fitBounds(bounds, { padding: 45, maxZoom: 12 });

        refreshMap = () => {
            const orderedStops = [...list.children]
                .map(item => stopsByOrderId.get(String(item.dataset.orderId)))
                .filter(Boolean);

            orderedStops.forEach((stop, index) => {
                const marker = markersByOrderId.get(String(stop.id));
                if (marker) marker.textContent = String(index + 1);
            });

            const startDepot = depots.find(depot => depot.kind === 'Start');
            const endDepot = depots.find(depot => depot.kind === 'Eind');
            const waypoints = [
                ...(startDepot ? [startDepot] : []),
                ...orderedStops,
                ...(endDepot ? [endDepot] : []),
            ];

            if (waypoints.length < 2) return;

            // Show the new order immediately while the road geometry is recalculated.
            currentGeometry = {
                type: 'LineString',
                coordinates: waypoints.map(point => [point.lng, point.lat])
            };
            renderRouteLine();

            window.clearTimeout(routeTimer);
            routeRequest?.abort();

            // Mapbox Directions accepts at most 25 coordinates in one request.
            if (waypoints.length > 25) return;

            routeTimer = window.setTimeout(() => {
                routeRequest = new AbortController();
                const pairs = waypoints.map(point => `${point.lng},${point.lat}`).join(';');
                fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${pairs}?overview=full&geometries=geojson&access_token=${token}`, {
                    signal: routeRequest.signal
                })
                    .then(response => response.json())
                    .then(data => {
                        const geometry = data.routes?.[0]?.geometry;
                        if (!geometry) return;
                        currentGeometry = geometry;
                        renderRouteLine();
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') console.warn('Routelijn bijwerken mislukt', error);
                    });
            }, 150);
        };
    });
})();
</script>
@endpush
@endif
