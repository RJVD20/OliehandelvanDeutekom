@extends('themes.default.layouts.app')

@section('title', 'Winkelmand')

@section('content')

<h1 class="text-3xl font-bold text-green-700 mb-6">Winkelmand</h1>

@if (count($cart) > 0)
    <div class="space-y-4">

        @php $total = 0; @endphp

        @foreach ($cart as $id => $item)
            @php
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            @endphp

            <div class="flex items-center justify-between bg-white p-4 border rounded-lg">

                <!-- Product info + quantity -->
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">
                        {{ $item['name'] }}
                    </h2>

                    <!-- Quantity controls -->
<div
    x-data="{ qty: {{ $item['quantity'] }} }"
    class="flex items-center gap-2"
>
    <button
        type="button"
        @click="
            if (qty > 1) {
                qty--;
                $nextTick(() => $refs.form.submit());
            }
        "
        class="px-2 py-1 border rounded hover:bg-gray-100"
    >−</button>

    <form
        x-ref="form"
        method="POST"
        action="{{ route('cart.update', $id) }}"
    >
        @csrf
        <input
            type="hidden"
            name="quantity"
            x-model="qty"
        >
        <span
            x-text="qty"
            class="w-6 text-center inline-block font-medium"
        ></span>
    </form>

    <button
        type="button"
        @click="
            qty++;
            $nextTick(() => $refs.form.submit());
        "
        class="px-2 py-1 border rounded hover:bg-gray-100"
    >+</button>
</div>

                </div>

                <!-- Price + remove -->
                <div class="flex items-center gap-6">
                    <span class="font-bold text-green-700">
                        € {{ number_format($subtotal, 2, ',', '.') }}
                    </span>

                    <form method="POST" action="{{ route('cart.remove', $id) }}">
                        @csrf
                        <button class="text-red-500 hover:underline text-sm">
                            Verwijderen
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

        <!-- Total -->
        <div class="flex justify-between items-center mt-6 border-t pt-4">
            <span class="text-lg font-semibold">
                Totaal
            </span>
            <span class="text-xl font-bold text-green-700">
                € {{ number_format($total, 2, ',', '.') }}
            </span>
        </div>

        <!-- Checkout -->
        <div class="text-right mt-6">
            <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <a
    href="{{ route('checkout.index') }}"
    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700"
>
    Afrekenen
</a>

            </button>
        </div>
    </div>
@else
    <p class="text-gray-500">
        Je winkelmand is leeg.
    </p>
@endif

@endsection
