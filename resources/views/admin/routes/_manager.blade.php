@push('head')
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
        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
            <div>
                <h2 class="text-lg font-semibold">Routes op deze datum</h2>
                <p class="mt-1 text-sm text-gray-500">Kies een route om de chauffeur, stops en verzending te beheren.</p>
            </div>
            <form id="existing-route-filter" method="GET" action="{{ route('admin.routes.index') }}">
                <label class="block space-y-1 text-sm">
                    <span class="font-medium text-gray-700">Bezorgdatum</span>
                    <input type="date" name="route_date" value="{{ $routeDate }}" class="w-full rounded-xl border-gray-300 md:w-48">
                </label>
            </form>
        </div>

        @if($deliveryRoutes->isNotEmpty())
            <div class="mt-5 grid gap-3 sm:grid-cols-[repeat(auto-fit,minmax(15rem,1fr))]">
                @foreach($deliveryRoutes as $deliveryRoute)
                    <a
                        href="{{ route('admin.routes.index', ['route_date' => $routeDate, 'route_id' => $deliveryRoute->id]) }}"
                        class="rounded-xl border p-4 transition {{ $selectedDeliveryRoute?->id === $deliveryRoute->id ? 'border-turbo-blue bg-blue-50 ring-2 ring-turbo-blue/10' : 'border-gray-200 bg-white hover:border-gray-300' }}"
                    >
                        <span class="flex items-start justify-between gap-3">
                            <strong class="min-w-0 truncate text-sm text-gray-900">{{ $deliveryRoute->name }}</strong>
                            <span class="shrink-0 rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-semibold text-gray-600">
                                {{ $deliveryRoute->orders_count ?? $deliveryRoute->orders()->count() }} stops
                            </span>
                        </span>
                        <span class="mt-2 block truncate text-xs text-gray-500">{{ $deliveryRoute->admin?->name ?? 'Geen chauffeur' }}</span>
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
                @if($selectedDeliveryRoute->startLocation || $selectedDeliveryRoute->endLocation)
                    <span class="block text-xs text-gray-500">
                        {{ $selectedDeliveryRoute->startLocation?->name ?? 'Geen startdepot' }}
                        →
                        {{ $selectedDeliveryRoute->endLocation?->name ?? 'Geen einddepot' }}
                    </span>
                @endif
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

            <div class="flex flex-col gap-2 sm:flex-row">
                <a
                    href="{{ route('admin.routes.loading', $selectedDeliveryRoute) }}"
                    class="w-full rounded-xl bg-green-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-green-700 lg:w-auto"
                >
                    Wagen laden
                </a>
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

                <form
                    method="POST"
                    action="{{ route('admin.routes.destroy', $selectedDeliveryRoute) }}"
                    onsubmit="return confirm('Deze route verwijderen? De {{ $existingRouteOrders->count() }} gekoppelde bestelling(en) worden weer ongepland en blijven behouden.')"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-700 hover:bg-red-50 lg:w-auto">
                        Route verwijderen
                    </button>
                </form>
            </div>
        </div>

        @if($existingRouteOrders->isEmpty())
            <div class="p-6 text-sm text-gray-500">Deze route bevat nog geen stops.</div>
        @else
            @php
                $storedTravelMinutes = (int) $existingRouteOrders->sum(fn ($order) => (int) ($order->route_travel_minutes ?? 0));
                $storedStopMinutes = (int) $existingRouteOrders->sum(fn ($order) => (int) ($order->route_stop_minutes ?? 0));
                $storedTotalMinutes = $storedTravelMinutes + $storedStopMinutes;
                $formatRouteMinutes = fn (int $minutes) => $minutes >= 60
                    ? floor($minutes / 60).'u '.($minutes % 60).'m'
                    : $minutes.' min';
            @endphp

            <div class="border-b border-gray-100 bg-gray-50/70 p-5">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <span class="text-xs font-medium text-gray-500">Stops</span>
                        <strong class="mt-1 block text-xl text-gray-900">{{ $existingRouteOrders->count() }}</strong>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <span class="text-xs font-medium text-gray-500">Reistijd</span>
                        <strong id="route-summary-travel" class="mt-1 block text-xl text-gray-900">{{ $formatRouteMinutes($storedTravelMinutes) }}</strong>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <span class="text-xs font-medium text-gray-500">Stoptijd</span>
                        <strong id="route-summary-stop" class="mt-1 block text-xl text-gray-900">{{ $formatRouteMinutes($storedStopMinutes) }}</strong>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <span class="text-xs font-medium text-gray-500">Totale duur</span>
                        <strong id="route-summary-total" class="mt-1 block text-xl text-gray-900">{{ $formatRouteMinutes($storedTotalMinutes) }}</strong>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <span class="text-xs font-medium text-gray-500">Afstand</span>
                        <strong id="route-summary-distance" class="mt-1 block text-xl text-gray-900">Wordt berekend…</strong>
                    </div>
                </div>
                <p id="route-summary-note" class="mt-3 text-xs text-gray-500">
                    @if(!$selectedDeliveryRoute->startLocation)
                        Reistijd naar de eerste stop ontbreekt omdat geen startlocatie is ingesteld.
                    @else
                        Tijden zijn schattingen op basis van de actuele route.
                    @endif
                </p>
            </div>

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
                                                <p class="mt-1 text-xs text-gray-500">
                                                    Reistijd:
                                                    <span data-travel-label>{{ $order->route_travel_minutes !== null ? $order->route_travel_minutes.' min' : 'wordt berekend…' }}</span>
                                                    · Stoptijd:
                                                    <span data-stop-label>{{ $order->route_stop_minutes !== null ? $order->route_stop_minutes.' min' : 'niet ingesteld' }}</span>
                                                </p>
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
        $existingMapDepots = collect([
            $selectedDeliveryRoute->startLocation ? [
                'kind' => 'Start',
                'name' => $selectedDeliveryRoute->startLocation->name,
                'address' => trim($selectedDeliveryRoute->startLocation->street.', '.$selectedDeliveryRoute->startLocation->postcode_city, ' ,'),
                'lng' => (float) $selectedDeliveryRoute->startLocation->lng,
                'lat' => (float) $selectedDeliveryRoute->startLocation->lat,
            ] : null,
            $selectedDeliveryRoute->endLocation ? [
                'kind' => 'Eind',
                'name' => $selectedDeliveryRoute->endLocation->name,
                'address' => trim($selectedDeliveryRoute->endLocation->street.', '.$selectedDeliveryRoute->endLocation->postcode_city, ' ,'),
                'lng' => (float) $selectedDeliveryRoute->endLocation->lng,
                'lat' => (float) $selectedDeliveryRoute->endLocation->lat,
            ] : null,
        ])->filter(fn ($depot) => $depot && $depot['lng'] && $depot['lat'])->values();
    @endphp
    <script>
    (async () => {
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
        const depots = @json($existingMapDepots);
        const routeGeometry = @json($routeGeometry);
        const mapElement = document.getElementById('existing-route-map');
        if (!token || !mapElement) return;

        if (!document.querySelector('link[data-mapbox-styles]')) {
            const stylesheet = document.createElement('link');
            stylesheet.rel = 'stylesheet';
            stylesheet.href = 'https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.css';
            stylesheet.dataset.mapboxStyles = 'true';
            document.head.appendChild(stylesheet);
        }

        if (!window.mapboxgl) {
            try {
                await new Promise((resolve, reject) => {
                    const existingScript = document.querySelector('script[data-mapbox-script]');
                    if (existingScript) {
                        existingScript.addEventListener('load', resolve, { once: true });
                        existingScript.addEventListener('error', reject, { once: true });
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = 'https://api.mapbox.com/mapbox-gl-js/v3.2.0/mapbox-gl.js';
                    script.async = true;
                    script.dataset.mapboxScript = 'true';
                    script.addEventListener('load', resolve, { once: true });
                    script.addEventListener('error', reject, { once: true });
                    document.head.appendChild(script);
                });
            } catch {
                mapElement.innerHTML = '<div class="flex h-full items-center justify-center p-6 text-sm text-gray-500">De routekaart kon niet worden geladen.</div>';
                return;
            }
        }

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

            let currentGeometry = routeGeometry;
            let renderTimer = null;
            const renderRouteLine = () => {
                if (!currentGeometry) return;

                if (!map.isStyleLoaded()) {
                    window.clearTimeout(renderTimer);
                    renderTimer = window.setTimeout(renderRouteLine, 100);
                    return;
                }

                const feature = {
                    type: 'Feature',
                    geometry: currentGeometry
                };

                if (map.getSource('managed-route')) {
                    map.getSource('managed-route').setData(feature);
                } else {
                    map.addSource('managed-route', { type: 'geojson', data: feature });
                }

                if (!map.getLayer('managed-route-outline')) {
                    map.addLayer({
                        id: 'managed-route-outline',
                        type: 'line',
                        source: 'managed-route',
                        layout: { 'line-cap': 'round', 'line-join': 'round' },
                        paint: { 'line-color': '#ffffff', 'line-width': 8, 'line-opacity': 0.95 }
                    });
                }
                if (!map.getLayer('managed-route-line')) {
                    map.addLayer({
                        id: 'managed-route-line',
                        type: 'line',
                        source: 'managed-route',
                        layout: { 'line-cap': 'round', 'line-join': 'round' },
                        paint: { 'line-color': '#0f766e', 'line-width': 5, 'line-opacity': 1 }
                    });
                }
            };

            map.on('style.load', () => window.setTimeout(renderRouteLine, 100));
            renderRouteLine();

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
            map.fitBounds(bounds, { padding: 45, maxZoom: 12 });

            const startDepot = depots.find(depot => depot.kind === 'Start');
            const endDepot = depots.find(depot => depot.kind === 'Eind');
            const waypoints = [
                ...(startDepot ? [{ lng: startDepot.lng, lat: startDepot.lat, orderId: null }] : []),
                ...coordinates.map(stop => ({ lng: stop.lng, lat: stop.lat, orderId: stop.id })),
                ...(endDepot ? [{ lng: endDepot.lng, lat: endDepot.lat, orderId: null }] : []),
            ];

            if (waypoints.length < 2) return;
            if (currentGeometry) return;

            const formatMinutes = minutes => {
                const rounded = Math.max(0, Math.round(minutes));
                return rounded >= 60
                    ? `${Math.floor(rounded / 60)}u ${rounded % 60}m`
                    : `${rounded} min`;
            };
            const pairs = waypoints.map(point => `${point.lng},${point.lat}`).join(';');
            fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${pairs}?overview=full&geometries=geojson&annotations=duration&access_token=${token}`)
                .then(response => response.json())
                .then(data => {
                    const route = data.routes?.[0];
                    const geometry = route?.geometry;
                    if (!geometry) return;
                    currentGeometry = geometry;
                    renderRouteLine();

                    route.legs?.forEach((leg, index) => {
                        const destinationOrderId = waypoints[index + 1]?.orderId;
                        if (!destinationOrderId) return;

                        const input = document.querySelector(
                            `[data-order-id="${destinationOrderId}"] input[name="route_travel_minutes"]`
                        );
                        if (input) {
                            const minutes = Math.max(0, Math.round((leg.duration || 0) / 60));
                            input.value = minutes;
                            const label = input.closest('[data-order-id]')?.querySelector('[data-travel-label]');
                            if (label) label.textContent = `${minutes} min`;
                        }
                    });

                    const travelMinutes = Math.round((route.duration || 0) / 60);
                    const stopMinutes = [...document.querySelectorAll(
                        '[data-order-id] input[name="route_stop_minutes"]'
                    )].reduce((total, input) => total + (Number(input.value) || 0), 0);

                    const travelSummary = document.getElementById('route-summary-travel');
                    const stopSummary = document.getElementById('route-summary-stop');
                    const totalSummary = document.getElementById('route-summary-total');
                    const distanceSummary = document.getElementById('route-summary-distance');

                    if (travelSummary) travelSummary.textContent = formatMinutes(travelMinutes);
                    if (stopSummary) stopSummary.textContent = formatMinutes(stopMinutes);
                    if (totalSummary) totalSummary.textContent = formatMinutes(travelMinutes + stopMinutes);
                    if (distanceSummary) {
                        distanceSummary.textContent = `${((route.distance || 0) / 1000).toLocaleString('nl-NL', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1
                        })} km`;
                    }
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
