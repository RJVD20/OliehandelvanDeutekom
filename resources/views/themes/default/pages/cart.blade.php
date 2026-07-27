@extends('themes.default.layouts.app')

@section('title', 'Winkelmand')

@section('content')

<p class="turbo-section-label mb-2">Jouw bestelling</p>
<h1 class="text-3xl font-bold mb-6">Winkelmand</h1>

@if (count($cart) > 0)
    <div class="space-y-4">

        @include('themes.default.components.delivery-notice', [
            'detailed' => true,
            'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'mb-6']),
        ])

        @php $total = 0; @endphp

        @foreach ($cart as $id => $item)
            @php
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            @endphp

            <div class="turbo-card flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4">

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
                    <span class="product-card__price">
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
            <span class="product-card__price text-xl">
                € {{ number_format($total, 2, ',', '.') }}
            </span>
        </div>

        <!-- Checkout -->
        <div class="text-right mt-6">
            <a href="{{ route('checkout.index') }}" class="turbo-button px-6 py-3">
                Afrekenen
            </a>
        </div>
    </div>
@else
    <div class="turbo-card p-8 text-center text-gray-500">Je winkelmand is leeg.</div>
@endif

@endsection
