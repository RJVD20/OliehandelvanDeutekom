@extends('themes.default.layouts.app')

@section('title', 'Afrekenen')

@section('content')

<p class="turbo-section-label mb-2">Veilig bestellen</p>
<h1 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8">
    Afrekenen
</h1>

@if (count($cart) === 0)
    <div class="bg-white border rounded-lg p-6 text-gray-600">
        Je winkelmand is leeg.
    </div>
@else

@if($fulfillmentMethod === 'delivery')
    @include('themes.default.components.delivery-notice', [
        'detailed' => true,
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'mb-6']),
    ])
@else
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
        <strong>Afhalen gekozen.</strong> Kies hieronder bij welk depot je de bestelling wilt ophalen.
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">

    <!-- Gegevens -->
    <div class="turbo-card p-5 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold mb-5">
            Jouw gegevens
        </h2>

@guest
    <div class="mb-6 p-4 border border-turbo-gold/30 rounded-lg bg-turbo-gray text-sm">
        <strong>Heb je al een account?</strong><br>
        <a href="{{ route('login') }}" class="text-green-700 underline">
            Log in
        </a>
        om je bestelling op te slaan en je adres automatisch te gebruiken.
    </div>
@endguest


        <form
            id="checkout-form"
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

            @if($fulfillmentMethod === 'pickup')
                <fieldset class="mb-6">
                    <legend class="text-base font-semibold text-turbo-ink">Kies je afhaaldepot</legend>
                    <p class="mt-1 text-sm text-gray-500">Je bestelling wordt klaargezet bij de gekozen locatie.</p>

                    @if($pickupLocations->isEmpty())
                        <div class="mt-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            Er zijn momenteel geen afhaaldepots beschikbaar. Kies in de winkelmand voor thuisbezorgen.
                        </div>
                    @else
                        <div class="mt-4 grid gap-3">
                            @foreach($pickupLocations as $location)
                                <label class="group relative cursor-pointer">
                                    <input
                                        type="radio"
                                        name="pickup_location_id"
                                        value="{{ $location->id }}"
                                        class="peer sr-only"
                                        required
                                        @checked((string) old('pickup_location_id') === (string) $location->id)
                                    >
                                    <span class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:ring-2 peer-checked:ring-emerald-100 group-hover:border-emerald-400">
                                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800">
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"/>
                                                <circle cx="12" cy="10" r="2"/>
                                            </svg>
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block font-semibold text-turbo-ink">{{ $location->name }}</span>
                                            <span class="mt-0.5 block text-sm text-gray-600">
                                                {{ $location->street }}@if($location->street && $location->postcode_city), @endif{{ $location->postcode_city }}
                                            </span>
                                            @if($location->opening)
                                                <span class="mt-1 block text-xs text-gray-500">Openingstijden: {{ $location->opening }}</span>
                                            @endif
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('pickup_location_id')
                        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </fieldset>
            @endif

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

        </form>
    </div>

    <div class="space-y-6 h-fit lg:sticky lg:top-32">
        <!-- Overzicht -->
        <div class="turbo-card p-5 sm:p-6">
            <h2 class="text-lg sm:text-xl font-semibold mb-5">
                Bestellingsoverzicht
            </h2>

            <div class="mb-4 rounded-xl px-4 py-3 text-sm font-semibold {{ $fulfillmentMethod === 'delivery' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                {{ $fulfillmentMethod === 'delivery' ? 'Thuisbezorgen' : 'Afhalen bij een depot' }}
                <a href="{{ route('cart.index') }}" class="float-right text-xs underline">Wijzigen</a>
            </div>

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
                            <small class="block text-gray-400">€ {{ number_format($item['price'], 2, ',', '.') }} per stuk</small>
                        </span>
                        <span>
                            € {{ number_format($subtotal, 2, ',', '.') }}
                        </span>
                    </div>
                @endforeach

                <div class="border-t pt-4 flex justify-between text-base font-semibold">
                    <span>Totaal</span>
                    <span class="product-card__price">
                        € {{ number_format($total, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Betaalmethode -->
        <div class="turbo-card p-5 sm:p-6">
            <fieldset>
                <legend class="text-lg sm:text-xl font-semibold text-turbo-ink">Betaalmethode</legend>
                <p class="mt-1 text-sm text-gray-500">Je gaat direct naar de gekozen beveiligde betaalomgeving.</p>

                <div class="mt-4 grid gap-3">
                    @foreach($paymentMethods as $method => $details)
                        <label class="group relative cursor-pointer">
                            <input
                                form="checkout-form"
                                type="radio"
                                name="payment_method"
                                value="{{ $method }}"
                                class="peer sr-only"
                                required
                                @checked(old('payment_method', 'ideal') === $method)
                            >
                            <span class="flex h-full items-start gap-3 rounded-xl border border-turbo-blue/20 bg-white p-4 peer-checked:border-turbo-gold peer-checked:bg-turbo-gold/10 peer-checked:ring-2 peer-checked:ring-turbo-gold/20 group-hover:border-turbo-gold/70">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-turbo-navy text-turbo-gold-light">
                                    @if($method === 'ideal')
                                        <span class="text-xs font-extrabold">iDEAL</span>
                                    @else
                                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                            <rect x="3" y="5" width="18" height="14" rx="2" />
                                            <path d="M3 10h18M7 15h4" />
                                        </svg>
                                    @endif
                                </span>
                                <span>
                                    <span class="block font-semibold text-turbo-ink">{{ $details['label'] }}</span>
                                    <span class="mt-1 block text-xs leading-relaxed text-gray-500">{{ $details['description'] }}</span>
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('payment_method')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>

            <button
                form="checkout-form"
                type="submit"
                class="turbo-button w-full mt-5 py-3.5 text-base"
            >
                Bestelling plaatsen
            </button>
        </div>
    </div>

</div>

@endif

@endsection
