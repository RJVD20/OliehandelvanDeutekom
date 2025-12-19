@extends('themes.default.layouts.app')

@section('title', 'Afrekenen')

@section('content')

<h1 class="text-3xl font-bold text-green-700 mb-8">
    Afrekenen
</h1>

@if (count($cart) === 0)
    <div class="bg-white border rounded-lg p-6 text-gray-600">
        Je winkelmand is leeg.
    </div>
@else

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

    <!-- Gegevens -->
    <div class="bg-white border rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-6">
            Jouw gegevens
        </h2>

@guest
    <div class="mb-6 p-4 border rounded-lg bg-green-50 text-sm">
        <strong>Heb je al een account?</strong><br>
        <a href="{{ route('login') }}" class="text-green-700 underline">
            Log in
        </a>
        om je bestelling op te slaan en je adres automatisch te gebruiken.
    </div>
@endguest


        <form method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Naam
                </label>
                <input
                    name="name"
                    value="{{ old('name', optional(auth()->user())->name) }}"
                    required
                    class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    E-mailadres
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', optional(auth()->user())->email) }}"
                    required
                    class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Adres
                </label>
                <input
                    name="address"
                    placeholder="Straat + huisnummer"
                    value="{{ old('address', optional(auth()->user())->address) }}"
                    required
                    class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Postcode
                    </label>
                    <input
                        name="postcode"
                        placeholder="Postcode + Toevoeging"
                        value="{{ old('postcode', optional(auth()->user())->postcode) }}"
                        required
                        class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Plaats
                    </label>
                    <input
                        name="city"
                        value="{{ old('city', optional(auth()->user())->city) }}"
                        required
                        class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                    >
                </div>
            </div>

            <button
                type="submit"
                class="w-full mt-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition"
            >
                Bestelling plaatsen
            </button>
        </form>
    </div>

    <!-- Overzicht -->
    <div class="bg-white border rounded-lg p-6 h-fit">
        <h2 class="text-xl font-semibold mb-6">
            Bestellingsoverzicht
        </h2>

        <div class="space-y-4 text-sm">
            @php $total = 0; @endphp

            @foreach ($cart as $item)
                @php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                @endphp

                <div class="flex justify-between">
                    <span class="text-gray-700">
                        {{ $item['quantity'] }}× {{ $item['name'] }}
                    </span>
                    <span>
                        € {{ number_format($subtotal, 2, ',', '.') }}
                    </span>
                </div>
            @endforeach

            <div class="border-t pt-4 flex justify-between text-base font-semibold">
                <span>Totaal</span>
                <span class="text-green-700">
                    € {{ number_format($total, 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

</div>

@endif

@endsection
