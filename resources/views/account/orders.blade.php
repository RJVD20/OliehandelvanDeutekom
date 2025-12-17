@extends('themes.default.layouts.app')

@section('title', 'Mijn bestellingen')

@section('content')

<h1 class="text-3xl font-bold mb-8">
    Mijn bestellingen
</h1>

@forelse(auth()->user()->orders()->latest()->get() as $order)
    <a
        href="{{ route('account.orders.show', $order) }}"
        class="block border rounded-lg p-5 mb-4 bg-white hover:shadow transition"
    >
        <div class="flex justify-between items-center mb-2">
            <div class="font-semibold">
                Bestelling #{{ $order->id }}
            </div>
            <span class="text-sm text-gray-500">
                {{ $order->created_at->format('d-m-Y') }}
            </span>
        </div>

        <div class="text-sm text-gray-600">
            {{ $order->items->count() }} producten
        </div>

        <div class="mt-2 font-semibold text-green-700">
            € {{ number_format($order->total, 2, ',', '.') }}
        </div>
    </a>
@empty
    <div class="bg-white border rounded-lg p-6 text-gray-600">
        Je hebt nog geen bestellingen geplaatst.
    </div>
@endforelse

@endsection
