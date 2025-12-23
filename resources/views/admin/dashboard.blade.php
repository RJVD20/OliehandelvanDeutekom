
@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
	<div class="bg-white p-4 rounded shadow">
		<div class="text-sm text-gray-500">Totaal producten</div>
		<div class="text-2xl font-semibold">{{ $totalProducts }}</div>
		<div class="text-xs text-gray-400">Actief: {{ $activeProducts }} — Inactief: {{ $inactiveProducts }}</div>
	</div>

	<div class="bg-white p-4 rounded shadow">
		<div class="text-sm text-gray-500">Totaal bestellingen</div>
		<div class="text-2xl font-semibold">{{ $totalOrders }}</div>
		<div class="text-xs text-gray-400">Recente bestellingen: {{ $recentOrders->count() }}</div>
	</div>

	<div class="bg-white p-4 rounded shadow">
		<div class="text-sm text-gray-500">Snelle acties</div>
		<div class="mt-3 flex flex-col gap-2">
			<a href="{{ route('admin.products.create') }}" class="inline-block px-3 py-2 bg-green-600 text-white rounded">Nieuw product</a>
			<a href="{{ route('admin.products.index') }}" class="inline-block px-3 py-2 bg-blue-600 text-white rounded">Maak producten beheer</a>
			<a href="{{ route('admin.orders.index') }}" class="inline-block px-3 py-2 bg-yellow-600 text-white rounded">Bekijk bestellingen</a>
		</div>
	</div>
</div>

<div class="bg-white p-4 rounded shadow">
	<h2 class="font-semibold mb-4">Recente bestellingen</h2>

	@if($recentOrders->isEmpty())
		<p class="text-sm text-gray-500">Geen recente bestellingen.</p>
	@else
		<div class="overflow-x-auto">
			<table class="w-full text-left text-sm">
				<thead>
					<tr class="border-b">
						<th class="p-2">#</th>
						<th class="p-2">Naam</th>
						<th class="p-2">Datum</th>
						<th class="p-2">Totaal</th>
						<th class="p-2"></th>
					</tr>
				</thead>
				<tbody>
					@foreach($recentOrders as $order)
						<tr class="border-b hover:bg-gray-50">
							<td class="p-2">{{ $order->id }}</td>
							<td class="p-2">{{ $order->name }}</td>
							<td class="p-2">{{ $order->created_at->format('d-m-Y') }}</td>
							<td class="p-2">€ {{ number_format($order->total, 2, ',', '.') }}</td>
							<td class="p-2 text-right">
								<a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600">Bekijken →</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	@endif
</div>

@endsection
