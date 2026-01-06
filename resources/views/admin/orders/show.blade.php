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
    <div class="bg-white p-6 rounded shadow space-y-2">
        <h2 class="font-semibold mb-3">Klant</h2>

        <p class="text-sm"><strong>Naam:</strong> {{ $order->name }}</p>
        <p class="text-sm"><strong>Email:</strong> {{ $order->email }}</p>

        <p class="mt-2 text-sm leading-relaxed">
            <strong>Adres:</strong><br>
            {{ $order->address }}<br>
            {{ $order->postcode }} {{ $order->city }}<br>
            {{ $order->province ?? 'Provincie onbekend' }}
        </p>
    </div>

    <!-- Bestelling -->
    <div class="bg-white p-6 rounded shadow lg:col-span-2">
        <h2 class="font-semibold mb-3">Producten</h2>

        <div class="hidden md:block overflow-x-auto">
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
        </div>

        <div class="md:hidden space-y-3">
            @foreach($order->items as $item)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold leading-tight">{{ $item->product_name }}</p>
                        <p class="text-sm text-gray-600">x{{ $item->quantity }}</p>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm">
                        <span class="text-gray-600">€ {{ number_format($item->price, 2, ',', '.') }} p/st</span>
                        <span class="font-semibold text-green-700">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-right mt-4 font-bold text-green-700 text-lg">
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

            <div class="space-y-1">
                <label class="block text-sm text-gray-600">Provincie</label>
                <select name="province" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
                    <option value="">— kies provincie —</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" @selected($order->province === $province)>
                            {{ $province }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-sm text-gray-600">Route datum</label>
                    <input
                        type="date"
                        name="route_date"
                        value="{{ optional($order->route_date)->format('Y-m-d') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
                    >
                </div>

                <div class="space-y-1">
                    <label class="block text-sm text-gray-600">Volgorde (nummer)</label>
                    <input
                        type="number"
                        name="route_sequence"
                        min="1"
                        value="{{ $order->route_sequence }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
                    >
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button class="w-full sm:w-auto px-5 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700" type="submit">
                    Opslaan
                </button>
                <a
                    href="{{ route('admin.orders.show', $order) }}"
                    class="w-full sm:w-auto px-5 py-3 border rounded-lg text-center text-gray-800 font-semibold"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded shadow space-y-3">
        <h2 class="font-semibold mb-3">Acties</h2>

        <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="space-y-3">
            @csrf

            <button
                class="w-full px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700"
            >
                ✉️ Verzending morgen mailen
            </button>
        </form>
    </div>
</div>

@endsection
