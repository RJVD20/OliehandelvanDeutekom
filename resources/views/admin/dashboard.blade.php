
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

		<div class="mt-4 pt-4 border-t">
			<div class="text-sm text-gray-500 mb-2">Onderhoudsmodus</div>
			<div class="text-xs text-gray-500 mb-3">
				Status:
				<span class="font-semibold {{ $maintenanceEnabled ? 'text-red-700' : 'text-green-700' }}">
					{{ $maintenanceEnabled ? 'AAN' : 'UIT' }}
				</span>
			</div>

			<form method="POST" action="{{ route('admin.maintenance.toggle') }}">
				@csrf
				<button type="submit"
					class="inline-block px-3 py-2 text-white rounded {{ $maintenanceEnabled ? 'bg-gray-800' : 'bg-red-600' }}">
					{{ $maintenanceEnabled ? 'Onderhoud UIT zetten' : 'Onderhoud AAN zetten' }}
				</button>
			</form>

			<p class="text-xs text-gray-400 mt-2">
				Bezoekers zien de onderhoudspagina; admins kunnen nog inloggen.
			</p>
		</div>
	</div>
</div>

	<div class="bg-white p-4 rounded shadow">
	<h2 class="font-semibold mb-4">Recente bestellingen</h2>

	@if($recentOrders->isEmpty())
		<p class="text-sm text-gray-500">Geen recente bestellingen.</p>
	@else
			<div class="hidden md:block overflow-x-auto">
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

			<div class="md:hidden space-y-3">
				@foreach($recentOrders as $order)
					<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm">
						<div class="flex items-start justify-between gap-3">
							<div>
								<p class="text-xs text-gray-500">Bestelling #{{ $order->id }}</p>
								<p class="font-semibold leading-tight">{{ $order->name }}</p>
							</div>
							<p class="text-sm text-gray-600">{{ $order->created_at->format('d-m-Y') }}</p>
						</div>
						<div class="mt-2 flex items-center justify-between text-sm">
							<span class="font-semibold text-green-700">€ {{ number_format($order->total, 2, ',', '.') }}</span>
							<a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-full bg-blue-600 px-3 py-1.5 text-white text-xs font-semibold">Bekijken</a>
						</div>
					</div>
				@endforeach
			</div>
	@endif
</div>

@endsection
