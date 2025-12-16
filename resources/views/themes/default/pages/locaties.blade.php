@extends('themes.default.layouts.app')

@section('title', 'Ophaallocaties')

@section('content')

@php
    $locationsJson = $locaties->map(fn($loc) => [
        'id' => 'loc-' . $loc->slug,
        'name' => $loc->name,
        'street' => $loc->street,
        'postcode_city' => $loc->postcode_city,
        'opening' => $loc->opening,
        'phone' => $loc->phone,
        'lat' => (float) $loc->lat,
        'lng' => (float) $loc->lng,
        'show_on_map' => $loc->show_on_map,
        'remark' => $loc->remark,
    ])->values();
@endphp

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map { min-height: 22rem; }
</style>

<div class="max-w-6xl mx-auto px-4 py-10">
    <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold mb-2">
            Ophaallocaties
        </h1>
        <p class="text-slate-600 max-w-2xl">
            Hier vind je al onze afhaallocaties met adres, openingstijden en een kaart.
        </p>
    </header>

<div class="grid gap-6 sm:grid-cols-2">

    <!-- KAART (1 kolom breed, 2 rijen hoog) -->
    <aside class="sm:row-span-2">
        <div class="bg-white rounded-2xl shadow-md border overflow-hidden h-full">
            <div class="px-4 pt-4 pb-2 border-b">
                <h2 class="text-sm font-semibold">
                    Kaart met ophaallocaties
                </h2>
                <p class="text-xs text-slate-500">
                    Klik op een locatie om in te zoomen.
                </p>
            </div>

            <div id="map" class="h-full min-h-[520px]"></div>
        </div>
    </aside>

    <!-- LOCATIE CARDS -->
    @foreach ($locaties as $loc)
        <article class="bg-white rounded-2xl shadow-sm border p-5 flex flex-col gap-3">
            <div>
                <h2 class="font-semibold text-lg">
                    {{ $loc->name }}
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $loc->street }}<br>
                    {{ $loc->postcode_city }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase text-slate-500">
                    Openingstijden
                </p>
                <p class="text-sm text-slate-700 whitespace-pre-line">
                    {{ $loc->opening }}
                </p>
            </div>

            @if ($loc->phone)
                <p class="text-sm">
                    <span class="font-medium">Tel.</span>
                    <a href="tel:{{ preg_replace('/\D+/', '', $loc->phone) }}"
                       class="text-emerald-700 hover:underline">
                        {{ $loc->phone }}
                    </a>
                </p>
            @endif

            <button
                onclick="focusLocation('loc-{{ $loc->slug }}')"
                class="mt-auto text-sm font-medium text-emerald-700 hover:underline"
            >
                Bekijk op kaart
            </button>
        </article>
    @endforeach

</div>



<script>
    const locations = @json($locationsJson);

    const mapLocations = locations.filter(l => l.lat && l.lng && l.show_on_map);

    const avgLat = mapLocations.reduce((s,l)=>s+l.lat,0)/mapLocations.length;
    const avgLng = mapLocations.reduce((s,l)=>s+l.lng,0)/mapLocations.length;

    const map = L.map('map').setView([avgLat, avgLng], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const markersById = {};

    mapLocations.forEach(loc => {
        const marker = L.marker([loc.lat, loc.lng]).addTo(map);
        marker.bindPopup(`<strong>${loc.name}</strong><br>${loc.street}<br>${loc.postcode_city}`);
        markersById[loc.id] = marker;
    });

    const group = L.featureGroup(Object.values(markersById));
    map.fitBounds(group.getBounds().pad(0.1));

    window.focusLocation = function(id) {
        const marker = markersById[id];
        if (!marker) return;
        map.setView(marker.getLatLng(), 12);
        marker.openPopup();
    }
</script>

@endsection
