@extends('themes.default.layouts.app')

@section('title', 'Afrekenen')

@section('content')

<h1 class="text-2xl sm:text-3xl font-bold text-green-700 mb-6 sm:mb-8">
    Afrekenen
</h1>

@if (count($cart) === 0)
    <div class="bg-white border rounded-lg p-6 text-gray-600">
        Je winkelmand is leeg.
    </div>
@else

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">

    <!-- Gegevens -->
    <div class="bg-white border rounded-lg p-5 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold mb-5">
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


        <form
            method="POST"
            action="{{ route('checkout.store') }}"
            class="space-y-4"
            x-data="{
                postcode: '{{ old('postcode', optional(auth()->user())->postcode) }}',
                huisnummer: '',
                straat: '',
                stad: '{{ old('city', optional(auth()->user())->city) }}',
                provincie: '{{ old('province', optional(auth()->user())->province) }}',
                loading: false,
                fout: null,
                async lookup() {
                    if (!this.postcode || !this.huisnummer) return;
                    this.loading = true;
                    this.fout = null;
                    try {
                        const params = new URLSearchParams({ postcode: this.postcode, huisnummer: this.huisnummer });
                        const res = await fetch('/api/postcode-lookup?' + params);
                        const data = await res.json();
                        if (!res.ok) {
                            this.fout = data.message || 'Postcode niet gevonden';
                        } else {
                            this.straat = data.straat;
                            this.stad = data.stad;
                            this.provincie = data.provincie;
                        }
                    } catch (e) {
                        this.fout = 'Verbindingsfout, vul handmatig in';
                    } finally {
                        this.loading = false;
                    }
                }
            }"
            @submit="if (huisnummer && straat) $el.querySelector('[name=address]').value = straat + ' ' + huisnummer"
        >
            @csrf

            {{-- Verborgen address veld: wordt gevuld bij submit --}}
            <input type="hidden" name="address" value="{{ old('address', optional(auth()->user())->address) }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                <input
                    name="name"
                    value="{{ old('name', optional(auth()->user())->name) }}"
                    required
                    class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mailadres</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', optional(auth()->user())->email) }}"
                    required
                    class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                >
            </div>

            {{-- Postcode + Huisnummer --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                    <input
                        name="postcode"
                        x-model="postcode"
                        placeholder="1234 AB"
                        required
                        class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Huisnummer</label>
                    <input
                        x-model="huisnummer"
                        placeholder="10"
                        required
                        @blur="lookup()"
                        class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                    >
                    <p x-show="fout" x-text="fout" class="mt-1 text-xs text-red-600"></p>
                </div>
            </div>

            {{-- Straat (auto-ingevuld) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Straat</label>
                <input
                    x-model="straat"
                    :placeholder="loading ? '...' : 'Wordt automatisch ingevuld'"
                    required
                    class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                >
            </div>

            {{-- Stad + Provincie --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plaats</label>
                    <input
                        name="city"
                        x-model="stad"
                        :placeholder="loading ? '...' : 'Wordt automatisch ingevuld'"
                        required
                        class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provincie</label>
                    <select
                        name="province"
                        x-model="provincie"
                        required
                        class="w-full border rounded-lg p-3 focus:ring focus:ring-green-200"
                    >
                        <option value="">Kies je provincie</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}">{{ $province }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button
                type="submit"
                class="w-full mt-6 py-3.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition text-base"
            >
                Bestelling plaatsen
            </button>
        </form>
    </div>

    <!-- Overzicht -->
    <div class="bg-white border rounded-lg p-5 sm:p-6 h-fit">
        <h2 class="text-lg sm:text-xl font-semibold mb-5">
            Bestellingsoverzicht
        </h2>

        <div class="space-y-3 sm:space-y-4 text-sm">
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
