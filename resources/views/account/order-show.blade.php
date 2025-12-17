@extends('themes.default.layouts.app')

@section('title', 'Bestelling #' . $order->id)

@section('content')

<h1 class="text-3xl font-bold mb-6">
    Bestelling #{{ $order->id }}
</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Producten -->
    <div class="lg:col-span-2 bg-white border rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">
            Producten
        </h2>

        <div class="space-y-3 text-sm">
            @foreach($order->items as $item)
                <div class="flex justify-between">
                    <span>
                        {{ $item->quantity }}× {{ $item->product_name }}
                    </span>
                    <span>
                        € {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="border-t mt-4 pt-4 flex justify-between font-semibold">
            <span>Totaal</span>
            <span class="text-green-700">
                € {{ number_format($order->total, 2, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Gegevens -->
    <div class="bg-white border rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">
            Gegevens
        </h2>

        <div class="text-sm space-y-2 text-gray-700">
            <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
            <div><strong>Naam:</strong> {{ $order->name }}</div>
            <div><strong>E-mail:</strong> {{ $order->email }}</div>
            <div><strong>Adres:</strong><br>
                {{ $order->address }}<br>
                {{ $order->postcode }} {{ $order->city }}
            </div>
        </div>
    </div>

</div>

@endsection
