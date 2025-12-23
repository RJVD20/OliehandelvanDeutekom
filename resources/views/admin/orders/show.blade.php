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
            {{ $order->postcode }} {{ $order->city }}
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

<!-- Acties -->
<div class="mt-6 bg-white p-6 rounded shadow">
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

@endsection
