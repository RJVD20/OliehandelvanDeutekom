@extends('admin.layouts.app')

@section('title', 'Bestellingen')

@section('content')
<h1 class="text-2xl font-bold mb-6">Bestellingen</h1>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="border-b">
            <th class="p-3 text-left">#</th>
            <th class="p-3">Naam</th>
            <th class="p-3">Datum</th>
            <th class="p-3">Totaal</th>
            <th class="p-3"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">{{ $order->id }}</td>
                <td class="p-3">{{ $order->name }}</td>
                <td class="p-3">{{ $order->created_at->format('d-m-Y') }}</td>
                <td class="p-3">€ {{ number_format($order->total, 2, ',', '.') }}</td>
                <td class="p-3 text-right">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-green-700">
                        Bekijken →
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $orders->links() }}
@endsection
