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

<section class="mb-8 rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="border-b border-gray-100 p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold">Routes op deze datum</h2>
                <p class="mt-1 text-sm text-gray-500">Kies een route om de chauffeur, stops en verzending te beheren.</p>
            </div>
            @if($selectedDeliveryRoute)
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                    {{ $existingRouteOrders->count() }} stops
                </span>
            @endif
        </div>

        <form id="existing-route-filter" method="GET" action="{{ route('admin.routes.index') }}" class="mt-4 max-w-xs">
            <label class="block space-y-1 text-sm">
                <span class="font-medium text-gray-700">Bezorgdatum</span>
                <input type="date" name="route_date" value="{{ $routeDate }}" class="w-full rounded-xl border-gray-300">
            </label>
        </form>

        @if($deliveryRoutes->isNotEmpty())
            <div class="-mx-1 mt-4 flex snap-x gap-3 overflow-x-auto px-1 pb-2">
                @foreach($deliveryRoutes as $deliveryRoute)
                    <a
                        href="{{ route('admin.routes.index', ['route_date' => $routeDate, 'route_id' => $deliveryRoute->id]) }}"
                        class="min-w-[15rem] snap-start rounded-xl border p-4 transition {{ $selectedDeliveryRoute?->id === $deliveryRoute->id ? 'border-turbo-blue bg-blue-50 ring-2 ring-turbo-blue/10' : 'border-gray-200 bg-white hover:border-gray-300' }}"
                    >
                        <strong class="block truncate text-sm text-gray-900">{{ $deliveryRoute->name }}</strong>
                        <span class="mt-2 flex items-center justify-between gap-3 text-xs text-gray-500">
                            <span>{{ $deliveryRoute->orders_count ?? $deliveryRoute->orders()->count() }} stops</span>
                            <span>{{ $deliveryRoute->admin?->name ?? 'Geen chauffeur' }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if(!$selectedDeliveryRoute)
        <div class="p-6 text-sm text-gray-500">Op deze datum zijn nog geen routes aangemaakt.</div>
    @else
        <div class="grid gap-4 border-b border-gray-100 p-5 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            <form method="POST" action="{{ route('admin.routes.assign-admin') }}" class="space-y-1">
                @csrf
                <input type="hidden" name="route_id" value="{{ $selectedDeliveryRoute->id }}">
                <span class="block text-base font-semibold text-gray-900">{{ $selectedDeliveryRoute->name }}</span>
                <span class="block text-xs text-gray-500">Chauffeur koppelen</span>
                <div class="flex gap-2">
                    <select name="admin_user_id" class="min-w-0 flex-1 rounded-xl border-gray-300">
                        <option value="">Nog niet toegewezen</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" @selected($selectedDeliveryRoute->admin_id === $admin->id)>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-xl bg-turbo-blue px-4 py-2.5 text-sm font-semibold text-white">Opslaan</button>
                </div>
            </form>

            @if($existingRouteOrders->isNotEmpty())
                <form
                    method="POST"
                    action="{{ route('admin.routes.ship', $selectedDeliveryRoute) }}"
                    onsubmit="return confirm('Alle bestellingen in deze route als verzonden markeren en de verzendmail sturen?')"
                >
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-amber-500 px-5 py-3 text-sm font-semibold text-turbo-navy lg:w-auto">
                        Verzendmail naar hele route
                    </button>
                </form>
            @endif
        </div>

        @if($existingRouteOrders->isEmpty())
            <div class="p-6 text-sm text-gray-500">Deze route bevat nog geen stops.</div>
        @else
            <div class="grid gap-6 p-5 xl:grid-cols-[minmax(0,1.05fr)_minmax(22rem,0.95fr)]">
                <div>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="font-semibold">Stops en volgorde</h3>
                            <p class="text-xs text-gray-500">Sleep stops en sla daarna de volgorde op.</p>
                        </div>
                        <form id="existing-resequence-form" method="POST" action="{{ route('admin.routes.resequence') }}">
                            @csrf
                            <input type="hidden" name="route_id" value="{{ $selectedDeliveryRoute->id }}">
                            <div id="existing-order-ids"></div>
                            <button type="submit" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white">Volgorde opslaan</button>
                        </form>
                    </div>

                    <div id="existing-route-list" class="space-y-2">
                        @foreach($existingRouteOrders as $order)
                            <article draggable="true" data-order-id="{{ $order->id }}" class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                                <div class="flex items-start gap-3 p-3">
                                    <span class="cursor-grab text-xl text-gray-400" aria-hidden="true">☰</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <strong class="text-sm">#{{ $order->id }} {{ $order->name }}</strong>
                                                <p class="text-xs text-gray-500">{{ $order->address }}, {{ $order->postcode }} {{ $order->city }}</p>
                                            </div>
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Stop {{ $order->route_sequence ?? '—' }}</span>
                                        </div>

                                    </div>
                                </div>

                                <details class="border-t border-gray-100">
                                    <summary class="cursor-pointer px-4 py-2 text-xs font-semibold text-turbo-blue hover:bg-gray-50">
                                        Details aanpassen
                                    </summary>
                                    <div class="bg-gray-50 p-4">
                                        <form method="POST" action="{{ route('admin.routes.timing', $order) }}" class="grid gap-3 sm:grid-cols-3">
                                                @csrf
                                                @method('PATCH')
                                                <label class="space-y-1 text-xs text-gray-600">
                                                    <span>Volgorde</span>
                                                    <input type="number" name="route_sequence" min="1" value="{{ $order->route_sequence }}" class="w-full rounded-lg border-gray-300 text-sm">
                                                </label>
                                                <label class="space-y-1 text-xs text-gray-600">
                                                    <span>Reistijd (min)</span>
                                                    <input type="number" name="route_travel_minutes" min="0" value="{{ $order->route_travel_minutes }}" class="w-full rounded-lg border-gray-300 text-sm">
                                                </label>
                                                <label class="space-y-1 text-xs text-gray-600">
                                                    <span>Stoptijd (min)</span>
                                                    <input type="number" name="route_stop_minutes" min="0" value="{{ $order->route_stop_minutes }}" class="w-full rounded-lg border-gray-300 text-sm">
                                                </label>
                                                <label class="space-y-1 text-xs text-gray-600 sm:col-span-3">
                                                    <span>Notities</span>
                                                    <textarea name="route_notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ $order->route_notes }}</textarea>
                                                </label>
                                                <button type="submit" class="justify-self-start rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white sm:col-span-3">Opslaan</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.routes.remove', $order) }}" class="mt-2" onsubmit="return confirm('Deze stop uit de route verwijderen?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-700">Stop uit route verwijderen</button>
                                            </form>
                                    </div>
                                </details>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="xl:sticky xl:top-6 xl:self-start">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="font-semibold">Routekaart</h3>
                        <span class="text-xs text-gray-500">Nummers volgen de stopvolgorde</span>
                    </div>
                    <div id="existing-route-map" class="h-[28rem] overflow-hidden rounded-xl bg-gray-100"></div>
                </div>
            </div>
        @endif
    @endif
</section>

@if($selectedDeliveryRoute && $existingRouteOrders->isNotEmpty())
    @push('scripts')
    @php
        $existingMapStops = $existingRouteOrders->values()->map(fn ($order, $index) => [
            'sequence' => $index + 1,
            'id' => $order->id,
            'name' => $order->name,
            'address' => $order->address,
            'postcode' => $order->postcode,
            'city' => $order->city,
            'province' => $order->province,
            'lng' => $order->geo_lng ? (float) $order->geo_lng : null,
            'lat' => $order->geo_lat ? (float) $order->geo_lat : null,
        ]);
    @endphp
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.js"></script>
    <script>
    (() => {
        const filter = document.getElementById('existing-route-filter');
        filter?.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('change', () => filter.requestSubmit());
        });

        const list = document.getElementById('existing-route-list');
        const hidden = document.getElementById('existing-order-ids');
        if (list && hidden) {
            const refresh = () => {
                hidden.innerHTML = '';
                [...list.children].forEach(item => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'order_ids[]';
                    input.value = item.dataset.orderId;
                    hidden.appendChild(input);
                });
            };
            let dragged = null;
            list.addEventListener('dragstart', event => {
                dragged = event.target.closest('[data-order-id]');
                dragged?.classList.add('opacity-50');
            });
            list.addEventListener('dragend', () => {
                dragged?.classList.remove('opacity-50');
                dragged = null;
                refresh();
            });
            list.addEventListener('dragover', event => {
                event.preventDefault();
                const target = event.target.closest('[data-order-id]');
                if (!dragged || !target || dragged === target) return;
                const before = event.clientY < target.getBoundingClientRect().top + target.offsetHeight / 2;
                list.insertBefore(dragged, before ? target : target.nextSibling);
            });
            refresh();
        }

        const token = @json($mapboxToken);
        const stops = @json($existingMapStops);
        const mapElement = document.getElementById('existing-route-map');
        if (!token || !mapElement || !window.mapboxgl) return;

        mapboxgl.accessToken = token;
        const map = new mapboxgl.Map({
            container: mapElement,
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [5.3, 52.1],
            zoom: 7
        });
        map.addControl(new mapboxgl.NavigationControl());

        let satellite = false;
        const styleButton = document.createElement('button');
        styleButton.type = 'button';
        styleButton.textContent = 'Satelliet';
        styleButton.className = 'absolute left-3 top-3 z-10 rounded bg-white px-3 py-2 text-xs font-bold shadow';
        mapElement.classList.add('relative');
        mapElement.appendChild(styleButton);
        styleButton.addEventListener('click', () => {
            satellite = !satellite;
            styleButton.textContent = satellite ? 'Kaart' : 'Satelliet';
            map.setStyle(satellite
                ? 'mapbox://styles/mapbox/satellite-streets-v12'
                : 'mapbox://styles/mapbox/streets-v12'
            );
        });

        const geocode = async stop => {
            if (stop.lng !== null && stop.lat !== null) return stop;
            const query = `${stop.address}, ${stop.postcode} ${stop.city}, ${stop.province || 'Nederland'}`;
            const response = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?limit=1&country=nl&access_token=${token}`);
            const center = (await response.json()).features?.[0]?.center;
            return center ? { ...stop, lng: center[0], lat: center[1] } : null;
        };

        Promise.all(stops.map(geocode)).then(results => {
            const coordinates = results.filter(Boolean);
            if (!coordinates.length) return;

            const bounds = new mapboxgl.LngLatBounds();
            coordinates.forEach(stop => {
                const marker = document.createElement('div');
                marker.className = 'smart-route-marker';
                marker.textContent = stop.sequence;
                new mapboxgl.Marker(marker)
                    .setLngLat([stop.lng, stop.lat])
                    .setPopup(new mapboxgl.Popup().setHTML(`<strong>#${stop.id} ${stop.name}</strong><br>${stop.address}<br>${stop.city}`))
                    .addTo(map);
                bounds.extend([stop.lng, stop.lat]);
            });
            map.fitBounds(bounds, { padding: 45, maxZoom: 12 });

            if (coordinates.length < 2) return;
            const pairs = coordinates.map(stop => `${stop.lng},${stop.lat}`).join(';');
            fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${pairs}?overview=full&geometries=geojson&access_token=${token}`)
                .then(response => response.json())
                .then(data => {
                    const geometry = data.routes?.[0]?.geometry;
                    if (!geometry) return;
                    const addLine = () => {
                        if (!map.isStyleLoaded()) return;
                        if (!map.getSource('managed-route')) {
                            map.addSource('managed-route', { type: 'geojson', data: { type: 'Feature', geometry } });
                            map.addLayer({
                                id: 'managed-route',
                                type: 'line',
                                source: 'managed-route',
                                paint: { 'line-color': '#0f766e', 'line-width': 5, 'line-opacity': 0.95 }
                            });
                        }
                    };
                    addLine();
                    map.on('style.load', addLine);
                });
        });
    })();
    </script>
    @endpush
@else
    @push('scripts')
    <script>
        document.getElementById('existing-route-filter')
            ?.querySelectorAll('input, select')
            .forEach(input => input.addEventListener('change', event => event.currentTarget.form.requestSubmit()));
    </script>
    @endpush
@endif
