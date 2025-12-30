@extends('admin.layouts.app')

@section('title', 'Bestellingen')

@section('content')
<h1 class="text-2xl font-bold mb-6">Bestellingen</h1>

<div class="bg-white rounded shadow p-4 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" method="GET">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Provincie</label>
            <select
                name="province"
                class="w-full border rounded p-2"
            >
                <option value="">Alle provincies</option>
                @foreach($provinces as $province)
                    <option
                        value="{{ $province }}"
                        @selected(($filters['province'] ?? '') === $province)
                    >
                        {{ $province }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Route datum</label>
            <input
                type="date"
                name="route_date"
                value="{{ $filters['route_date'] ?? '' }}"
                class="w-full border rounded p-2"
            >
        </div>

        <label class="inline-flex items-center space-x-2">
            <input
                type="checkbox"
                name="only_planned"
                value="1"
                @checked(request('only_planned'))
                class="border rounded"
            >
            <span class="text-sm text-gray-700">Alleen geplande routes</span>
        </label>

        <div class="flex space-x-2 justify-end md:justify-start">
            <button class="px-4 py-2 bg-green-600 text-white rounded" type="submit">
                Filteren
            </button>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 border rounded text-gray-700">
                Reset
            </a>
        </div>
    </form>
</div>

<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="border-b">
            <th class="p-3 text-left">#</th>
            <th class="p-3">Naam</th>
            <th class="p-3">Provincie</th>
            <th class="p-3">Datum</th>
            <th class="p-3">Route</th>
            <th class="p-3">Totaal</th>
            <th class="p-3"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">{{ $order->id }}</td>
                <td class="p-3">{{ $order->name }}</td>
                <td class="p-3">{{ $order->province ?? 'n.v.t.' }}</td>
                <td class="p-3">{{ $order->created_at->format('d-m-Y') }}</td>
                <td class="p-3">
                    @if($order->route_date)
                        {{ $order->route_date->format('d-m-Y') }}
                        @if($order->route_sequence)
                            <span class="text-xs text-gray-500">(#{{ $order->route_sequence }})</span>
                        @endif
                    @else
                        <span class="text-gray-400">Nog niet gepland</span>
                    @endif
                </td>
                <td class="p-3">€ {{ number_format($order->total, 2, ',', '.') }}</td>
                <td class="p-3 text-right">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-green-700">
                        Bekijken →
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">{{ $orders->links() }}</div>
@endsection
