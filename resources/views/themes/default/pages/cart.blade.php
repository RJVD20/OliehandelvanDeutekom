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
                <div>
                    <h2 class="font-semibold">{{ $item['name'] }}</h2>
                        <form method="POST" action="{{ route('cart.update', $id) }}" class="flex items-center gap-2">
                            @csrf
                            <input
                                type="number"
                                name="quantity"
                                value="{{ $item['quantity'] }}"
                                min="1"
                                class="w-16 border rounded text-center"
                            >
                            <button class="text-sm text-green-600 hover:underline">
                                Update
                            </button>
                        </form>
                </div>
                <div class="flex items-center gap-4">
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

        <div class="flex justify-between items-center mt-6 border-t pt-4">
            <span class="text-lg font-semibold">Totaal</span>
            <span class="text-xl font-bold text-green-700">
                € {{ number_format($total, 2, ',', '.') }}
            </span>
        </div>

        <div class="text-right mt-6">
            <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Afrekenen
            </button>
        </div>
    </div>
@else
    <p class="text-gray-500">Je winkelmand is leeg.</p>
@endif

@endsection
