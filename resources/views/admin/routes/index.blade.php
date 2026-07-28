@extends('admin.layouts.app')

@section('title', 'Routes')

@push('head')
<link
    href="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.css"
    rel="stylesheet"
>
<style>
    #route-map { height: 360px; }
    @media (min-width: 768px) { #route-map { height: 480px; } }
    .drag-handle { cursor: grab; }
    .route-marker {
        background: #16a34a;
        color: #fff;
        border-radius: 9999px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border: 2px solid #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.25);
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold">Routeplanning</h1>
        <p class="text-sm text-gray-500 mt-1">Maak routes per dag, koppel chauffeurs en beheer de stops.</p>
    </div>
    <a href="{{ route('admin.routes.smart') }}" class="rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
        ✨ Slim route plannen
    </a>
</div>

<div class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(20rem,0.65fr)]">
        <form id="route-filter-form" method="GET" action="{{ route('admin.routes.index') }}" class="grid gap-4 sm:grid-cols-3">
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Datum</span>
                <input type="date" name="route_date" value="{{ $filters['route_date'] ?? $routeDate }}" class="w-full rounded-xl border-gray-300">
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Provincie</span>
                <select name="province" class="w-full rounded-xl border-gray-300">
                    <option value="">Alle provincies</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" @selected(($filters['province'] ?? '') === $province)>{{ $province }}</option>
                    @endforeach
                </select>
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Route</span>
                <select name="route_id" class="w-full rounded-xl border-gray-300" @disabled($routes->isEmpty())>
                    @if($routes->isEmpty())
                        <option value="">Geen routes op deze datum</option>
                    @else
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" @selected($selectedRoute?->id === $route->id)>{{ $route->name }}</option>
                        @endforeach
                    @endif
                </select>
            </label>
        </form>

        <form method="POST" action="{{ route('admin.routes.assign-admin') }}" class="border-t border-gray-100 pt-4 xl:border-l xl:border-t-0 xl:pl-5 xl:pt-0">
            @csrf
            <input type="hidden" name="route_id" value="{{ $selectedRoute?->id }}">
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Chauffeur voor deze route</span>
                <div class="flex gap-2">
                    <select name="admin_user_id" class="min-w-0 flex-1 rounded-xl border-gray-300" @disabled(!$selectedRoute)>
                        <option value="">Nog niet toegewezen</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" @selected($assignedAdminId === $admin->id)>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" @disabled(!$selectedRoute) class="rounded-xl bg-turbo-blue px-4 font-semibold text-white disabled:opacity-40">Opslaan</button>
                </div>
            </label>
            <p class="mt-2 text-xs text-gray-500">
                {{ $selectedRoute ? 'Geopend: '.$selectedRoute->name : 'Kies eerst een datum en route.' }}
            </p>
        </form>
    </div>

    <details class="mt-4 border-t border-gray-100 pt-4">
        <summary class="cursor-pointer text-sm font-semibold text-gray-600">Meer acties</summary>
        <form method="POST" action="{{ route('admin.routes.store') }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            @csrf
            <input type="hidden" name="route_date" value="{{ $filters['route_date'] ?? $routeDate }}">
            <input type="hidden" name="province" value="{{ $filters['province'] ?? '' }}">
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Lege route aanmaken</span>
                <input name="name" placeholder="Naam van de route" required class="w-full rounded-xl border-gray-300">
            </label>
            <label class="space-y-1 text-sm">
                <span class="font-medium text-gray-700">Chauffeur</span>
                <select name="admin_user_id" class="w-full rounded-xl border-gray-300">
                    <option value="">Later kiezen</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Lege route maken</button>
        </form>
    </details>
</div>

@if(!$selectedRoute)
    <div class="bg-white border border-dashed rounded-2xl p-6 text-gray-600">Maak of selecteer eerst een route.</div>
@elseif($orders->isEmpty())
    <div class="bg-white border border-dashed rounded-2xl p-6 text-gray-600">Geen stops gevonden voor deze route.</div>
@else
<div class="mb-6 flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="font-semibold text-gray-900">Verzending doorgeven</h2>
        <p class="mt-1 text-sm text-gray-600">
            Stuur de verzendmail naar alle {{ $orders->count() }} bestellingen in {{ $selectedRoute->name }}.
        </p>
    </div>
    <form
        method="POST"
        action="{{ route('admin.routes.ship', $selectedRoute) }}"
        onsubmit="return confirm('Weet je zeker dat je alle bestellingen in deze route als verzonden wilt markeren en de verzendmail wilt sturen?')"
    >
        @csrf
        <button type="submit" class="w-full rounded-xl bg-turbo-blue px-5 py-3 text-sm font-semibold text-white shadow-sm hover:opacity-90 sm:w-auto">
            Verzendmail naar hele route
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold">Volgorde</h2>
            <form id="resequence-form" method="POST" action="{{ route('admin.routes.resequence') }}">
                @csrf
                <input type="hidden" name="route_id" value="{{ $selectedRoute?->id }}">
                <div id="order-ids"></div>
                <button class="px-3 py-2 bg-green-600 text-white rounded-lg text-xs font-semibold" type="submit">Volgorde opslaan</button>
            </form>
        </div>
        <p class="text-xs text-gray-500 mb-3">Sleep stops om de volgorde te bepalen.</p>
        <ul id="route-list" class="space-y-3">
            @foreach($orders as $order)
                <li
                    class="border border-gray-200 rounded-xl p-3 bg-gray-50 flex items-start gap-3"
                    data-order-id="{{ $order->id }}"
                    draggable="true"
                >
                    <span class="drag-handle text-xl">☰</span>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">#{{ $order->id }} — {{ $order->name }}</div>
                            <div class="text-xs text-gray-600">{{ $order->province ?? 'n.v.t.' }}</div>
                        </div>
                        <div class="text-sm text-gray-700">{{ $order->address }}, {{ $order->postcode }} {{ $order->city }}</div>
                        <div class="text-xs text-gray-500 mt-1">Huidige volgorde: {{ $order->route_sequence ?? '—' }}</div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold">Kaart</h2>
            <span class="text-xs text-gray-600">Marker nummers volgen de volgorde</span>
        </div>
        <div id="route-map" class="rounded"></div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
    @php
        $totalTravel = $orders->sum('route_travel_minutes');
        $totalStop   = $orders->sum('route_stop_minutes');
        $totalRoute  = $totalTravel + $totalStop;
    @endphp

    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold">Tijden en notities</h2>
        <div class="text-sm text-gray-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
            Totale route: {{ $totalRoute ?: 0 }} min
            @if($totalRoute)
                (≈ {{ floor($totalRoute / 60) }}u {{ $totalRoute % 60 }}m)
            @endif
        </div>
    </div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="p-2 text-left">Stop</th>
                        <th class="p-2">Volgorde</th>
                        <th class="p-2">Reistijd (min)</th>
                        <th class="p-2">Stop (min)</th>
                        <th class="p-2">Notities</th>
                        <th class="p-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b align-top">
                            <td class="p-2">
                                <div class="font-semibold">#{{ $order->id }} {{ $order->name }}</div>
                                <div class="text-gray-600">{{ $order->address }}, {{ $order->postcode }} {{ $order->city }}</div>
                            </td>
                            <td class="p-2 text-center">
                                <form method="POST" action="{{ route('admin.routes.timing', $order) }}" class="space-y-2">
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="number"
                                        name="route_sequence"
                                        min="1"
                                        class="w-20 rounded border border-gray-300 px-2 py-2 text-center text-sm"
                                        value="{{ $order->route_sequence }}"
                                    >
                            </td>
                            <td class="p-2 text-center">
                                    <input
                                        type="number"
                                        name="route_travel_minutes"
                                        min="0"
                                        class="w-24 rounded border border-gray-300 px-2 py-2 text-center text-sm"
                                        value="{{ $order->route_travel_minutes }}"
                                    >
                            </td>
                            <td class="p-2 text-center">
                                    <input
                                        type="number"
                                        name="route_stop_minutes"
                                        min="0"
                                        class="w-24 rounded border border-gray-300 px-2 py-2 text-center text-sm"
                                        value="{{ $order->route_stop_minutes }}"
                                    >
                            </td>
                            <td class="p-2">
                                    <textarea
                                        name="route_notes"
                                        class="w-full rounded border border-gray-300 p-2 text-sm"
                                        rows="2"
                                    >{{ $order->route_notes }}</textarea>
                            </td>
                            <td class="p-2 text-right align-top space-y-2">
                                    <button class="px-3 py-2 text-xs bg-green-600 text-white rounded-lg inline-flex items-center justify-center" type="submit">Opslaan</button>
                            </form>
                            <form method="POST" action="{{ route('admin.routes.remove', $order) }}">
                                @csrf
                                @method('PATCH')
                                <button class="px-3 py-2 text-xs bg-red-600 text-white rounded-lg inline-flex items-center justify-center" type="submit">Verwijder</button>
                            </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            @foreach($orders as $order)
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Stop #{{ $order->id }}</p>
                            <p class="font-semibold leading-tight">{{ $order->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->address }}, {{ $order->postcode }} {{ $order->city }}</p>
                        </div>
                        <span class="text-sm text-gray-600">Volgorde: {{ $order->route_sequence ?? '—' }}</span>
                    </div>

                    <form method="POST" action="{{ route('admin.routes.timing', $order) }}" class="mt-3 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="space-y-1 text-sm text-gray-700">
                                <span>Volgorde</span>
                                <input type="number" name="route_sequence" min="1" value="{{ $order->route_sequence }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-base">
                            </label>
                            <label class="space-y-1 text-sm text-gray-700">
                                <span>Reistijd (min)</span>
                                <input type="number" name="route_travel_minutes" min="0" value="{{ $order->route_travel_minutes }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-base">
                            </label>
                            <label class="space-y-1 text-sm text-gray-700">
                                <span>Stop (min)</span>
                                <input type="number" name="route_stop_minutes" min="0" value="{{ $order->route_stop_minutes }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-base">
                            </label>
                        </div>

                        <div class="space-y-1 text-sm text-gray-700">
                            <span>Notities</span>
                            <textarea name="route_notes" rows="2" class="w-full rounded-lg border border-gray-300 p-3 text-base">{{ $order->route_notes }}</textarea>
                        </div>

                        <button class="w-full rounded-lg bg-green-600 px-4 py-3 text-white font-semibold" type="submit">Opslaan</button>
                    </form>

                    <form method="POST" action="{{ route('admin.routes.remove', $order) }}" class="mt-3">
                        @csrf
                        @method('PATCH')
                        <button class="w-full rounded-lg bg-red-600 px-4 py-3 text-white font-semibold" type="submit">Verwijder</button>
                    </form>
                </div>
            @endforeach
        </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.js"></script>
<script>
    (function() {
        const list = document.getElementById('route-list');
        if (!list) return;

        const hiddenContainer = document.getElementById('order-ids');

        const refreshOrderIds = () => {
            hiddenContainer.innerHTML = '';
            [...list.querySelectorAll('li')].forEach((li) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'order_ids[]';
                input.value = li.dataset.orderId;
                hiddenContainer.appendChild(input);
            });
        };

        let dragged;
        list.addEventListener('dragstart', (e) => {
            dragged = e.target;
            e.dataTransfer.effectAllowed = 'move';
        });
        list.addEventListener('dragover', (e) => {
            e.preventDefault();
            const li = e.target.closest('li');
            if (!li || li === dragged) return;
            const rect = li.getBoundingClientRect();
            const before = (e.clientY - rect.top) / (rect.height || 1) < 0.5;
            li.parentNode.insertBefore(dragged, before ? li : li.nextSibling);
        });
        list.addEventListener('drop', (e) => {
            e.preventDefault();
            refreshOrderIds();
        });
        refreshOrderIds();
    })();

    // Open the chosen date, province or route immediately.
    (function() {
        const form = document.getElementById('route-filter-form');
        if (!form) return;
        const inputs = form.querySelectorAll('input[name="route_date"], select[name="province"], select[name="route_id"]');

        const submitForm = () => form.requestSubmit ? form.requestSubmit() : form.submit();

        inputs.forEach((el) => el.addEventListener('change', submitForm));
    })();

    (async function() {
        const mapEl = document.getElementById('route-map');
        if (!mapEl) return;

        @php
            $stopsPayload = $orders->map(function ($o) {
                return [
                    'id'       => $o->id,
                    'name'     => $o->name,
                    'address'  => $o->address,
                    'postcode' => $o->postcode,
                    'city'     => $o->city,
                    'province' => $o->province,
                ];
            })->values();
        @endphp

        const stops = @json($stopsPayload);

        const mapboxToken = @json($mapboxToken);
        if (!mapboxToken) {
            mapEl.innerHTML = '<div class="p-3 text-sm text-gray-600">Mapbox token ontbreekt. Voeg MAPBOX_TOKEN toe aan .env.</div>';
            return;
        }

        if (!stops.length) {
            mapEl.innerHTML = '<div class="p-3 text-sm text-gray-600">Geen stops voor deze selectie.</div>';
            return;
        }

        mapboxgl.accessToken = mapboxToken;
        const map = new mapboxgl.Map({
            container: 'route-map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [5.3, 52.1],
            zoom: 7,
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
        let currentRouteGeometry = null;

        const renderRouteLine = () => {
            if (!currentRouteGeometry || !map.isStyleLoaded()) return;

            if (!map.getSource('route-line')) {
                map.addSource('route-line', {
                    type: 'geojson',
                    data: {
                        type: 'Feature',
                        geometry: currentRouteGeometry,
                    },
                });
                map.addLayer({
                    id: 'route-line-layer',
                    type: 'line',
                    source: 'route-line',
                    paint: {
                        'line-color': '#16a34a',
                        'line-width': 4,
                        'line-opacity': 0.9,
                    },
                });
            } else {
                map.getSource('route-line').setData({
                    type: 'Feature',
                    geometry: currentRouteGeometry,
                });
            }
        };

        map.on('style.load', renderRouteLine);

        const geocode = async (stop) => {
            const query = `${stop.address}, ${stop.postcode} ${stop.city}, ${stop.province ?? 'Nederland'}`;
            const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?limit=1&country=nl&language=nl&access_token=${mapboxToken}`;
            try {
                const res = await fetch(url);
                const data = await res.json();
                if (!data.features || !data.features.length) return null;
                const [lon, lat] = data.features[0].center;
                return { lat, lon };
            } catch (e) {
                console.warn('Geocode mislukt', e);
                return null;
            }
        };

        const coords = [];
        for (const stop of stops) {
            const c = await geocode(stop);
            if (!c) continue;
            coords.push({ ...c, stop });
        }

        if (!coords.length) {
            mapEl.innerHTML = '<div class="p-3 text-sm text-gray-600">Kon adressen niet geocoderen. Controleer adressen of probeer een provincie filter.</div>';
            return;
        }

        map.on('load', async () => {
            const bounds = new mapboxgl.LngLatBounds();

            coords.forEach((c, idx) => {
                const el = document.createElement('div');
                el.className = 'route-marker';
                el.textContent = String(idx + 1);

                new mapboxgl.Marker(el)
                    .setLngLat([c.lon, c.lat])
                    .setPopup(new mapboxgl.Popup().setHTML(
                        `<strong>#${c.stop.id}</strong> ${c.stop.name}<br>${c.stop.address}<br>${c.stop.postcode} ${c.stop.city}`
                    ))
                    .addTo(map);

                bounds.extend([c.lon, c.lat]);
            });

            if (!bounds.isEmpty()) {
                map.fitBounds(bounds, { padding: 30, maxZoom: 12 });
            }

            if (coords.length > 1) {
                const travelInputs = document.querySelectorAll('input[name="route_travel_minutes"]');
                const coordPairs = coords.map(c => `${c.lon},${c.lat}`).join(';');
                const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${coordPairs}?overview=full&geometries=geojson&annotations=duration&access_token=${mapboxToken}`;

                try {
                    const res = await fetch(url);
                    const data = await res.json();

                    // Polyline via echte route geometrie
                    const geometry = data.routes?.[0]?.geometry;
                    if (geometry && geometry.coordinates?.length) {
                        currentRouteGeometry = geometry;
                        renderRouteLine();
                    }

                    // Reistijden per leg vullen
                    if (data.routes?.[0]?.legs && travelInputs.length) {
                        data.routes[0].legs.forEach((leg, idx) => {
                            const minutes = Math.round((leg.duration || 0) / 60);
                            const input = travelInputs[idx + 1];
                            if (input) input.value = minutes;
                        });
                    }
                } catch (e) {
                    console.warn('Directions-opvraag mislukt', e);
                }
            }
        });
    })();
</script>
@endpush
