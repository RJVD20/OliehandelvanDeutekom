<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

class SmartRoutePlanner
{
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

        if (! $token) {
            $warnings[] = 'Mapbox is niet ingesteld; de volgorde is bepaald op postcode.';
        } elseif (count($coordinates) < 2) {
            $warnings[] = 'Er zijn te weinig geldige adressen om de route automatisch te optimaliseren.';
        } elseif (count($coordinates) > 25) {
            $warnings[] = 'Mapbox optimaliseert maximaal 25 stops; de volgorde is bepaald op postcode.';
        } else {
            $result = $this->optimize($coordinates, $token);

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

        return [
            'orders' => $ordered,
            'coordinates' => collect($coordinates)->keyBy('order_id'),
            'warnings' => array_values(array_unique($warnings)),
            'travel_minutes' => $travelMinutes,
            'stop_minutes' => $ordered->count() * 10,
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
}
