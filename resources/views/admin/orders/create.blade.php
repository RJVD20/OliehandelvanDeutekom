@extends('admin.layouts.app')

@section('title', 'Handmatige bestelling')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold">Handmatige bestelling</h1>
        <p class="mt-1 text-sm text-gray-500">Voer hier een telefonische of andere handmatige bestelling in.</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 font-semibold text-gray-700">
        Terug naar bestellingen
    </a>
</div>

@if($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold">Controleer de ingevulde gegevens:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    method="POST"
    action="{{ route('admin.orders.store') }}"
    class="space-y-6"
    x-data='{
        items: @json(old("items", [["product_id" => "", "quantity" => 1]])),
        fulfillment: "{{ old("fulfillment_method", "delivery") }}",
        addItem() { this.items.push({ product_id: "", quantity: 1 }) },
        removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1) }
    }'
>
    @csrf

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-xl bg-white p-5 shadow">
            <h2 class="mb-4 text-lg font-semibold">Klantgegevens</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Naam *</label>
                    <input id="name" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">Telefoonnummer *</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">E-mailadres</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="sm:col-span-2" x-show="fulfillment === 'delivery'" x-transition>
                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700">Adres *</label>
                    <input id="address" name="address" value="{{ old('address') }}"
                        :required="fulfillment === 'delivery'" class="w-full rounded-lg border-gray-300">
                </div>
                <div x-show="fulfillment === 'delivery'" x-transition>
                    <label for="postcode" class="mb-1 block text-sm font-medium text-gray-700">Postcode *</label>
                    <input id="postcode" name="postcode" value="{{ old('postcode') }}"
                        :required="fulfillment === 'delivery'" placeholder="1234 AB" class="w-full rounded-lg border-gray-300">
                </div>
                <div x-show="fulfillment === 'delivery'" x-transition>
                    <label for="city" class="mb-1 block text-sm font-medium text-gray-700">Plaats *</label>
                    <input id="city" name="city" value="{{ old('city') }}"
                        :required="fulfillment === 'delivery'" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="sm:col-span-2" x-show="fulfillment === 'delivery'" x-transition>
                    <label for="province" class="mb-1 block text-sm font-medium text-gray-700">Provincie *</label>
                    <select id="province" name="province" :required="fulfillment === 'delivery'"
                        class="w-full rounded-lg border-gray-300">
                        <option value="">Kies een provincie</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}" @selected(old('province') === $province)>{{ $province }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section class="rounded-xl bg-white p-5 shadow">
            <h2 class="mb-4 text-lg font-semibold">Afhandeling</h2>
            <div class="space-y-4">

                {{-- Bezorgen / Afhalen --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Leveringswijze *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            :class="fulfillment === 'delivery' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200 bg-white'"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors"
                        >
                            <input type="radio" name="fulfillment_method" value="delivery"
                                x-model="fulfillment" class="text-green-600">
                            <div>
                                <span class="block text-sm font-semibold text-gray-800">🚚 Bezorgen</span>
                                <span class="text-xs text-gray-500">Levering op adres</span>
                            </div>
                        </label>
                        <label
                            :class="fulfillment === 'pickup' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 bg-white'"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors"
                        >
                            <input type="radio" name="fulfillment_method" value="pickup"
                                x-model="fulfillment" class="text-blue-600">
                            <div>
                                <span class="block text-sm font-semibold text-gray-800">🏪 Afhalen</span>
                                <span class="text-xs text-gray-500">Bij een depot</span>
                            </div>
                        </label>
                    </div>
                    @error('fulfillment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Depot kiezen (alleen bij afhalen) --}}
                <div x-show="fulfillment === 'pickup'" x-transition>
                    <label for="pickup_location_id" class="mb-1 block text-sm font-medium text-gray-700">Afhaallocatie (depot) *</label>
                    @if($locations->isEmpty())
                        <p class="rounded-lg bg-yellow-50 border border-yellow-200 p-3 text-sm text-yellow-800">
                            Er zijn nog geen locaties/depots toegevoegd.
                            <a href="{{ route('admin.locations.create') }}" class="underline font-semibold">Voeg er een toe →</a>
                        </p>
                    @else
                        <select id="pickup_location_id" name="pickup_location_id"
                            :required="fulfillment === 'pickup'"
                            class="w-full rounded-lg border-gray-300">
                            <option value="">— Kies een depot —</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('pickup_location_id') == $location->id)>
                                    {{ $location->name }}
                                    @if($location->postcode_city) — {{ $location->postcode_city }} @endif
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('pickup_location_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="payment_handling" class="mb-1 block text-sm font-medium text-gray-700">Betaling *</label>
                    <select id="payment_handling" name="payment_handling" required class="w-full rounded-lg border-gray-300">
                        <option value="pay_on_delivery" @selected(old('payment_handling', 'pay_on_delivery') === 'pay_on_delivery')>Betalen bij levering</option>
                        <option value="payment_link" @selected(old('payment_handling') === 'payment_link')>Betaallink aanmaken</option>
                        <option value="bank_transfer" @selected(old('payment_handling') === 'bank_transfer')>Betaalt via bankoverschrijving</option>
                        <option value="paid_cash" @selected(old('payment_handling') === 'paid_cash')>Contant betaald</option>
                        <option value="paid_bank" @selected(old('payment_handling') === 'paid_bank')>Per bank betaald</option>
                    </select>
                </div>
                <div>
                    <label for="payment_method" class="mb-1 block text-sm font-medium text-gray-700">Betaalmethode voor betaallink</label>
                    <select id="payment_method" name="payment_method" class="w-full rounded-lg border-gray-300">
                        <option value="">Automatisch laten kiezen</option>
                        @foreach(config('payments.methods', []) as $key => $method)
                            <option value="{{ $key }}" @selected(old('payment_method') === $key)>{{ $method['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <input type="checkbox" name="send_confirmation" value="1" @checked(old('send_confirmation', true)) class="mt-1 rounded border-gray-300">
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">Bevestigingsmail versturen</span>
                        <span class="block text-xs text-gray-500">Hiervoor moet een e-mailadres zijn ingevuld.</span>
                    </span>
                </label>
                <div>
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">Interne notitie</label>
                    <textarea id="notes" name="notes" rows="4" class="w-full rounded-lg border-gray-300" placeholder="Bijvoorbeeld bezorgafspraak of telefonische toelichting">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>
    </div>

    <section class="rounded-xl bg-white p-5 shadow">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold">Producten</h2>
                <p class="text-sm text-gray-500">De actuele webshopprijs wordt opgeslagen.</p>
            </div>
            <button type="button" @click="addItem()" class="rounded-lg bg-turbo-blue px-4 py-2 text-sm font-semibold text-white">
                + Product toevoegen
            </button>
        </div>

        @if($products->isEmpty())
            <p class="rounded-lg bg-yellow-50 p-4 text-sm text-yellow-800">Er zijn geen actieve producten beschikbaar.</p>
        @else
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid items-end gap-3 rounded-lg border border-gray-200 p-3 sm:grid-cols-[1fr_8rem_auto]">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Product *</label>
                            <select :name="`items[${index}][product_id]`" x-model="item.product_id" required class="w-full rounded-lg border-gray-300">
                                <option value="">Kies een product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} — € {{ number_format($product->price, 2, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Aantal *</label>
                            <input type="number" min="1" max="999" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required class="w-full rounded-lg border-gray-300">
                        </div>
                        <button type="button" @click="removeItem(index)" :disabled="items.length === 1" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 disabled:cursor-not-allowed disabled:opacity-40">
                            Verwijderen
                        </button>
                    </div>
                </template>
            </div>
        @endif
    </section>

    <div class="flex justify-end">
        <button type="submit" @disabled($products->isEmpty()) class="rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700 disabled:opacity-50">
            Bestelling aanmaken
        </button>
    </div>
</form>
@endsection
