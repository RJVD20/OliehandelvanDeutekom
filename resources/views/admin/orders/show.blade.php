@extends('admin.layouts.app')

@section('title', 'Bestelling #' . $order->id)

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Bestelling #{{ $order->id }}
</h1>

<div class="mb-4">
    <span class="px-3 py-1 rounded text-sm
        @if($order->status === 'pending') bg-yellow-100 text-yellow-700
        @elseif($order->status === 'shipped') bg-blue-100 text-blue-700
        @elseif($order->status === 'completed') bg-green-100 text-green-700
        @endif
    ">
        Status: {{ ucfirst($order->status) }}
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Klantgegevens -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="font-semibold mb-3">Klant</h2>

        <p><strong>Naam:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>

        <p class="mt-2">
            <strong>Adres:</strong><br>
            {{ $order->address }}<br>
            {{ $order->postcode }} {{ $order->city }}<br>
            {{ $order->province ?? 'Provincie onbekend' }}
        </p>
    </div>

    <!-- Bestelling -->
    <div class="bg-white p-6 rounded shadow lg:col-span-2">
        <h2 class="font-semibold mb-3">Producten</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Product</th>
                    <th class="p-2">Aantal</th>
                    <th class="p-2">Prijs</th>
                    <th class="p-2">Subtotaal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item->product_name }}</td>
                        <td class="p-2 text-center">{{ $item->quantity }}</td>
                        <td class="p-2 text-right">
                            € {{ number_format($item->price, 2, ',', '.') }}
                        </td>
                        <td class="p-2 text-right">
                            € {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mt-4 font-bold text-green-700">
            Totaal: € {{ number_format($order->total, 2, ',', '.') }}
        </div>
    </div>

</div>

<!-- Routeplanning en acties -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="font-semibold mb-3">Route plannen</h2>

        <form method="POST" action="{{ route('admin.orders.plan', $order) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm text-gray-600 mb-1">Provincie</label>
                <select name="province" class="w-full border rounded p-2">
                    <option value="">— kies provincie —</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" @selected($order->province === $province)>
                            {{ $province }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Route datum</label>
                    <input
                        type="date"
                        name="route_date"
                        value="{{ optional($order->route_date)->format('Y-m-d') }}"
                        class="w-full border rounded p-2"
                    >
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Volgorde (nummer)</label>
                    <input
                        type="number"
                        name="route_sequence"
                        min="1"
                        value="{{ $order->route_sequence }}"
                        class="w-full border rounded p-2"
                    >
                </div>
            </div>

            <div class="flex space-x-3">
                <button class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700" type="submit">
                    Opslaan
                </button>
                <a
                    href="{{ route('admin.orders.show', $order) }}"
                    class="px-5 py-2 border rounded text-gray-700"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h2 class="font-semibold mb-3">Acties</h2>

        <form method="POST" action="{{ route('admin.orders.ship', $order) }}">
            @csrf

            <button
                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            >
                ✉️ Verzending morgen mailen
            </button>
        </form>
    </div>
</div>

@endsection
