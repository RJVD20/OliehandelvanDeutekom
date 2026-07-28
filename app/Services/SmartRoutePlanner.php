<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

class SmartRoutePlanner
{
    public function __construct(
        private readonly RouteOptimizationUsage $usage
    ) {}

    public function createProposal(Collection $orders): array
    {
        $orders = $orders->sortBy([
            fn (Order $order) => $order->postcode ?? '',
            fn (Order $order) => $order->city ?? '',
            fn (Order $order) => $order->address ?? '',
        ])->values();

        $token = config('services.mapbox.token');
        $coordinates = [];
        $warnings = [];

        foreach ($orders as $order) {
            $coordinate = $this->coordinateFor($order, $token);

            if ($coordinate === null) {
                $warnings[] = "Adres van bestelling #{$order->id} kon niet op de kaart worden gevonden.";
                continue;
            }

            $coordinates[] = [
                'order_id' => $order->id,
                'lng' => $coordinate[0],
                'lat' => $coordinate[1],
            ];
        }

        $orderedIds = $orders->pluck('id')->all();
        $travelMinutes = null;
        $optimizationProvider = 'postcode';

        if (! $token) {
            $warnings[] = 'Mapbox is niet ingesteld; de volgorde is bepaald op postcode.';
        } elseif (count($coordinates) < 2) {
            $warnings[] = 'Er zijn te weinig geldige adressen om de route automatisch te optimaliseren.';
        } elseif (count($coordinates) > 25) {
            $warnings[] = 'Een route kan maximaal 25 stops bevatten; de volgorde is bepaald op postcode.';
        } else {
            $result = null;

            if (count($coordinates) >= 3 && $this->usage->claimGoogleRequest()) {
                $result = $this->optimizeWithGoogle($coordinates);
                $optimizationProvider = $result !== null ? 'google' : 'mapbox';

                if ($result === null) {
                    $warnings[] = 'Google Routes was niet beschikbaar; Mapbox is automatisch gebruikt.';
                }
            }

            if ($result === null) {
                $result = count($coordinates) <= 12
                    ? $this->optimize($coordinates, $token)
                    : $this->optimizeWithMatrix($coordinates, $token);

                if ($result !== null) {
                    $optimizationProvider = 'mapbox';
                }
            }

            if ($result !== null) {
                $optimizedIds = $result['order_ids'];
                $missingIds = array_values(array_diff($orderedIds, $optimizedIds));
                $orderedIds = [...$optimizedIds, ...$missingIds];
                $travelMinutes = $result['travel_minutes'];
            } else {
                $warnings[] = 'Automatische optimalisatie is niet gelukt; de volgorde is bepaald op postcode.';
            }
        }

        $ordersById = $orders->keyBy('id');
        $ordered = collect($orderedIds)
            ->map(fn (int $id) => $ordersById->get($id))
            ->filter()
            ->values();

        $route = $token
            ? $this->directions($orderedIds, $coordinates, $token)
            : null;

        if ($route !== null) {
            $travelMinutes = $route['travel_minutes'];
        } elseif ($token && count($coordinates) >= 2) {
            $warnings[] = 'De routelijn kon niet worden berekend; alleen de stops worden getoond.';
        }

        return [
            'orders' => $ordered,
            'coordinates' => collect($coordinates)->keyBy('order_id'),
            'warnings' => array_values(array_unique($warnings)),
            'travel_minutes' => $travelMinutes,
            'route_geometry' => $route['geometry'] ?? null,
            'stop_minutes' => $ordered->count() * 10,
            'optimization_provider' => $optimizationProvider,
        ];
    }

    private function coordinateFor(Order $order, ?string $token): ?array
    {
        $addressHash = hash('sha256', strtolower(trim(
            $order->address.'|'.$order->postcode.'|'.$order->city.'|'.($order->province ?? '')
        )));

        if ($order->geo_lat && $order->geo_lng && $order->geo_address_hash === $addressHash) {
            return [(float) $order->geo_lng, (float) $order->geo_lat];
        }

        if (! $token) {
            return null;
        }

        $query = trim($order->address.', '.$order->postcode.' '.$order->city.', '.($order->province ?? 'Nederland'));
        try {
            $response = Http::timeout(8)->get(
                'https://api.mapbox.com/geocoding/v5/mapbox.places/'.urlencode($query).'.json',
                [
                    'access_token' => $token,
                    'limit' => 1,
                    'country' => 'nl',
                    'language' => 'nl',
                ]
            );
        } catch (Throwable) {
            return null;
        }

        $center = $response->ok() ? $response->json('features.0.center') : null;
        if (! is_array($center) || ! isset($center[0], $center[1])) {
            return null;
        }

        $order->update([
            'geo_lng' => $center[0],
            'geo_lat' => $center[1],
            'geo_address_hash' => $addressHash,
        ]);

        return [(float) $center[0], (float) $center[1]];
    }

    private function optimize(array $coordinates, string $token): ?array
    {
        $pairs = collect($coordinates)
            ->map(fn (array $coordinate) => $coordinate['lng'].','.$coordinate['lat'])
            ->implode(';');

        try {
            $response = Http::timeout(12)->get(
                'https://api.mapbox.com/optimized-trips/v1/mapbox/driving/'.$pairs,
                [
                    'access_token' => $token,
                    'roundtrip' => 'false',
                    'source' => 'first',
                    'destination' => 'last',
                    'overview' => 'false',
                ]
            );
        } catch (Throwable) {
            return null;
        }

        $waypoints = $response->ok() ? $response->json('waypoints') : null;
        if (! is_array($waypoints) || count($waypoints) !== count($coordinates)) {
            return null;
        }

        $orderedIds = collect($coordinates)
            ->map(fn (array $coordinate, int $index) => [
                'order_id' => $coordinate['order_id'],
                'sequence' => $waypoints[$index]['waypoint_index'] ?? $index,
            ])
            ->sortBy('sequence')
            ->pluck('order_id')
            ->values()
            ->all();

        $duration = $response->json('trips.0.duration');

        return [
            'order_ids' => $orderedIds,
            'travel_minutes' => is_numeric($duration) ? (int) ceil($duration / 60) : null,
        ];
    }

    private function optimizeWithGoogle(array $coordinates): ?array
    {
        $first = $coordinates[0];
        $last = $coordinates[array_key_last($coordinates)];
        $intermediates = collect($coordinates)
            ->slice(1, max(0, count($coordinates) - 2))
            ->values();

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'X-Goog-Api-Key' => config('services.google_routes.key'),
                    'X-Goog-FieldMask' => 'routes.optimizedIntermediateWaypointIndex,routes.duration',
                ])
                ->post(config('services.google_routes.endpoint'), [
                    'origin' => $this->googleWaypoint($first),
                    'destination' => $this->googleWaypoint($last),
                    'intermediates' => $intermediates
                        ->map(fn (array $coordinate) => $this->googleWaypoint($coordinate))
                        ->all(),
                    'travelMode' => 'DRIVE',
                    'routingPreference' => 'TRAFFIC_UNAWARE',
                    'optimizeWaypointOrder' => true,
                ]);
        } catch (Throwable) {
            return null;
        }

        $route = $response->ok() ? $response->json('routes.0') : null;
        if (! is_array($route)) {
            return null;
        }

        $optimizedIndexes = $route['optimizedIntermediateWaypointIndex'] ?? [];
        if (count($optimizedIndexes) !== $intermediates->count()) {
            return null;
        }

        $orderedIds = [
            $first['order_id'],
            ...collect($optimizedIndexes)
                ->map(fn (int $index) => $intermediates->get($index)['order_id'] ?? null)
                ->filter()
                ->all(),
            $last['order_id'],
        ];

        if (count($orderedIds) !== count($coordinates)) {
            return null;
        }

        $duration = $route['duration'] ?? null;
        $durationSeconds = is_string($duration)
            ? (float) rtrim($duration, 's')
            : null;

        return [
            'order_ids' => $orderedIds,
            'travel_minutes' => is_numeric($durationSeconds) ? (int) ceil($durationSeconds / 60) : null,
        ];
    }

    private function googleWaypoint(array $coordinate): array
    {
        return [
            'location' => [
                'latLng' => [
                    'latitude' => (float) $coordinate['lat'],
                    'longitude' => (float) $coordinate['lng'],
                ],
            ],
        ];
    }

    private function optimizeWithMatrix(array $coordinates, string $token): ?array
    {
        $pairs = collect($coordinates)
            ->map(fn (array $coordinate) => $coordinate['lng'].','.$coordinate['lat'])
            ->implode(';');

        try {
            $response = Http::timeout(15)->get(
                'https://api.mapbox.com/directions-matrix/v1/mapbox/driving/'.$pairs,
                [
                    'access_token' => $token,
                    'annotations' => 'duration',
                ]
            );
        } catch (Throwable) {
            return null;
        }

        $durations = $response->ok() ? $response->json('durations') : null;
        if (! is_array($durations) || count($durations) !== count($coordinates)) {
            return null;
        }

        $order = [0];
        $unvisited = array_values(array_diff(array_keys($coordinates), [0]));
        $current = 0;
        $travelSeconds = 0;

        while ($unvisited !== []) {
            $next = collect($unvisited)
                ->filter(fn (int $index) => is_numeric($durations[$current][$index] ?? null))
                ->sortBy(fn (int $index) => $durations[$current][$index])
                ->first();

            if ($next === null) {
                return null;
            }

            $travelSeconds += (float) $durations[$current][$next];
            $order[] = $next;
            $unvisited = array_values(array_diff($unvisited, [$next]));
            $current = $next;
        }

        return [
            'order_ids' => collect($order)
                ->map(fn (int $index) => $coordinates[$index]['order_id'])
                ->all(),
            'travel_minutes' => (int) ceil($travelSeconds / 60),
        ];
    }

    private function directions(array $orderedIds, array $coordinates, string $token): ?array
    {
        $coordinatesByOrder = collect($coordinates)->keyBy('order_id');
        $pairs = collect($orderedIds)
            ->map(fn (int $orderId) => $coordinatesByOrder->get($orderId))
            ->filter()
            ->map(fn (array $coordinate) => $coordinate['lng'].','.$coordinate['lat'])
            ->values();

        if ($pairs->count() < 2 || $pairs->count() > 25) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get(
                'https://api.mapbox.com/directions/v5/mapbox/driving/'.$pairs->implode(';'),
                [
                    'access_token' => $token,
                    'overview' => 'full',
                    'geometries' => 'geojson',
                    'steps' => 'false',
                ]
            );
        } catch (Throwable) {
            return null;
        }

        $geometry = $response->ok() ? $response->json('routes.0.geometry') : null;
        $duration = $response->json('routes.0.duration');

        if (! is_array($geometry) || ($geometry['type'] ?? null) !== 'LineString') {
            return null;
        }

        return [
            'geometry' => $geometry,
            'travel_minutes' => is_numeric($duration) ? (int) ceil($duration / 60) : null,
        ];
    }
}
