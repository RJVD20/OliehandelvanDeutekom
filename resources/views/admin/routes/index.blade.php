@extends('admin.layouts.app')

@section('title', 'Routes')

@push('head')
<link
    href="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.css"
    rel="stylesheet"
>
<style>
    #route-map { height: 420px; }
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
<h1 class="text-2xl font-bold mb-6">Routeplanning</h1>

<div class="bg-white rounded shadow p-4 mb-6">
    <form id="route-filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" method="GET">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Route datum</label>
            <input
                type="date"
                name="route_date"
                value="{{ $filters['route_date'] ?? $routeDate }}"
                class="w-full border rounded p-2"
            >
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Provincie</label>
            <select name="province" class="w-full border rounded p-2">
                <option value="">Alle provincies</option>
                @foreach($provinces as $province)
                    <option value="{{ $province }}" @selected(($filters['province'] ?? '') === $province)>
                        {{ $province }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2 flex justify-end md:justify-start">
            <a href="{{ route('admin.routes.index') }}" class="px-4 py-2 border rounded text-gray-700">Reset</a>
        </div>
    </form>
</div>

@if($orders->isEmpty())
    <div class="bg-white border rounded p-6 text-gray-600">Geen stops gevonden voor deze datum/provincie.</div>
@else
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold">Volgorde (sleep om te herschikken)</h2>
            <form id="resequence-form" method="POST" action="{{ route('admin.routes.resequence') }}">
                @csrf
                <input type="hidden" name="route_date" value="{{ $routeDate }}">
                <input type="hidden" name="province" value="{{ $filters['province'] ?? '' }}">
                <div id="order-ids"></div>
                <button class="px-3 py-2 bg-green-600 text-white rounded" type="submit">Volgorde opslaan</button>
            </form>
        </div>
        <ul id="route-list" class="space-y-3">
            @foreach($orders as $order)
                <li
                    class="border rounded p-3 bg-gray-50 flex items-start gap-3"
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

    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold">Kaart</h2>
            <span class="text-xs text-gray-600">Marker nummers volgen de volgorde</span>
        </div>
        <div id="route-map" class="rounded"></div>
    </div>
</div>

<div class="bg-white rounded shadow p-4 mt-6">
    @php
        $totalTravel = $orders->sum('route_travel_minutes');
        $totalStop   = $orders->sum('route_stop_minutes');
        $totalRoute  = $totalTravel + $totalStop;
    @endphp

    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold">Tijden en notities</h2>
        <div class="text-sm text-gray-700 bg-gray-100 px-3 py-1 rounded">
            Totale route: {{ $totalRoute ?: 0 }} min
            @if($totalRoute)
                (≈ {{ floor($totalRoute / 60) }}u {{ $totalRoute % 60 }}m)
            @endif
        </div>
    </div>
    <div class="overflow-x-auto">
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
                                <input type="hidden" name="route_date" value="{{ $routeDate }}">
                                <input type="hidden" name="province" value="{{ $filters['province'] ?? '' }}">

                                <input
                                    type="number"
                                    name="route_sequence"
                                    min="1"
                                    class="w-16 border rounded p-1 text-center"
                                    value="{{ $order->route_sequence }}"
                                >
                        </td>
                        <td class="p-2 text-center">
                                <input
                                    type="number"
                                    name="route_travel_minutes"
                                    min="0"
                                    class="w-20 border rounded p-1 text-center"
                                    value="{{ $order->route_travel_minutes }}"
                                >
                        </td>
                        <td class="p-2 text-center">
                                <input
                                    type="number"
                                    name="route_stop_minutes"
                                    min="0"
                                    class="w-20 border rounded p-1 text-center"
                                    value="{{ $order->route_stop_minutes }}"
                                >
                        </td>
                        <td class="p-2">
                                <textarea
                                    name="route_notes"
                                    class="w-full border rounded p-2"
                                    rows="2"
                                >{{ $order->route_notes }}</textarea>
                        </td>
                        <td class="p-2 text-right align-top space-y-2">
                                <button class="px-2 py-1 text-xs bg-green-600 text-white rounded inline-flex items-center justify-center" type="submit">Opslaan</button>
                            </form>
                            <form method="POST" action="{{ route('admin.routes.remove', $order) }}">
                                @csrf
                                @method('PATCH')
                                <button class="px-2 py-1 text-xs bg-red-600 text-white rounded inline-flex items-center justify-center" type="submit">Verwijder</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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

    // Auto-apply filters (date/province) without a submit button
    (function() {
        const form = document.getElementById('route-filter-form');
        if (!form) return;
        const inputs = form.querySelectorAll('input[name="route_date"], select[name="province"]');

        const submitForm = () => form.requestSubmit ? form.requestSubmit() : form.submit();

        inputs.forEach((el) => {
            el.addEventListener('change', submitForm);
            el.addEventListener('input', (e) => {
                if (e.target.tagName === 'INPUT') submitForm();
            });
        });
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
                        if (!map.getSource('route-line')) {
                            map.addSource('route-line', {
                                type: 'geojson',
                                data: {
                                    type: 'Feature',
                                    geometry,
                                },
                            });

                            map.addLayer({
                                id: 'route-line-layer',
                                type: 'line',
                                source: 'route-line',
                                paint: {
                                    'line-color': '#16a34a',
                                    'line-width': 4,
                                    'line-opacity': 0.8,
                                },
                            });
                        } else {
                            map.getSource('route-line').setData({
                                type: 'Feature',
                                geometry,
                            });
                        }
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
