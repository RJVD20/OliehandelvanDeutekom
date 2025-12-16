@extends('themes.default.layouts.app')

@section('title', 'Checkout')

@section('content')

<h1 class="text-3xl font-bold text-green-700 mb-6">
    Afrekenen
</h1>

@if (count($cart) === 0)
    <p class="text-gray-500">
        Je winkelmand is leeg.
    </p>
@else
<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

    <!-- Gegevens -->
    <form class="space-y-4">
        <h2 class="text-xl font-semibold mb-2">
            Gegevens
        </h2>

        <input class="w-full border rounded p-2" placeholder="Naam">
        <input class="w-full border rounded p-2" placeholder="E-mail">
        <input class="w-full border rounded p-2" placeholder="Adres">
        <input class="w-full border rounded p-2" placeholder="Postcode">
        <input class="w-full border rounded p-2" placeholder="Plaats">
    </form>

    <!-- Overzicht -->
    <div>
        <h2 class="text-xl font-semibold mb-4">
            Bestelling
        </h2>

        <div class="space-y-3 border rounded p-4 bg-white">
            @php $total = 0; @endphp

            @foreach ($cart as $item)
                @php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                @endphp

                <div class="flex justify-between text-sm">
                    <span>{{ $item['quantity'] }}× {{ $item['name'] }}</span>
                    <span>€ {{ number_format($subtotal, 2, ',', '.') }}</span>
                </div>
            @endforeach

            <div class="border-t pt-2 flex justify-between font-semibold">
                <span>Totaal</span>
                <span class="text-green-700">
                    € {{ number_format($total, 2, ',', '.') }}
                </span>
            </div>
        </div>

        <button class="mt-6 w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Bestelling plaatsen
        </button>
    </div>

</div>
@endif

@endsection
