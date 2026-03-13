@extends('admin.layouts.app')

@section('title', 'Bestellingen')

@section('content')
<h1 class="text-2xl font-bold mb-6">Bestellingen</h1>

<div class="bg-white rounded shadow p-4 mb-6">
    <form id="order-filter-form" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end" method="GET">
        <div class="space-y-1">
            <label class="block text-sm text-gray-600">Route datum</label>
            <input
                type="date"
                name="route_date"
                value="{{ $filters['route_date'] ?? '' }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
            >
        </div>
        <div class="space-y-1">
            <label class="block text-sm text-gray-600">Besteldatum</label>
            <input
                type="date"
                name="order_date"
                value="{{ $filters['order_date'] ?? '' }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
            >
        </div>

        <div class="space-y-1">
            <label class="block text-sm text-gray-600">Provincie</label>
            <select name="province" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
                <option value="">Alle provincies</option>
                @foreach($provinces as $province)
                    <option
                        value="{{ $province }}"
                        @selected(($filters['province'] ?? '') === $province)
                    >
                        {{ $province }}
                    </option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-sm font-medium text-gray-800">
            <input
                type="checkbox"
                name="only_planned"
                value="1"
                @checked(request('only_planned'))
                class="h-5 w-5 rounded border-gray-300"
            >
            <span>Alleen geplande routes</span>
        </label>

        <div class="md:col-span-1 flex flex-col sm:flex-row gap-3 justify-end md:justify-start">
            <button
                type="button"
                data-open-bulk
                class="w-full md:w-auto px-4 py-3 bg-green-600 text-white rounded-lg text-center font-semibold"
            >
                Bulk in route
            </button>
            <a href="{{ route('admin.orders.index') }}" class="w-full md:w-auto px-4 py-3 border rounded-lg text-center text-gray-800 font-semibold">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="hidden md:block">
    <table class="w-full bg-white rounded shadow text-sm">
        <thead>
            <tr class="border-b">
                <th class="p-3 text-left">#</th>
                <th class="p-3">Naam</th>
                <th class="p-3">Provincie</th>
                <th class="p-3">Datum</th>
                <th class="p-3">Route</th>
                <th class="p-3">Totaal</th>
                <th class="p-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $order->id }}</td>
                    <td class="p-3">{{ $order->name }}</td>
                    <td class="p-3">{{ $order->province ?? 'n.v.t.' }}</td>
                    <td class="p-3">{{ $order->created_at->format('d-m-Y') }}</td>
                    <td class="p-3">
                        @if($order->route_date)
                            {{ $order->route_date->format('d-m-Y') }}
                            @if($order->route_sequence)
                                <span class="text-xs text-gray-500">(#{{ $order->route_sequence }})</span>
                            @endif
                        @else
                            <span class="text-gray-400">Nog niet gepland</span>
                        @endif
                    </td>
                    <td class="p-3">€ {{ number_format($order->total, 2, ',', '.') }}</td>
                    <td class="p-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" action="{{ route('admin.orders.ship', $order) }}">
                                @csrf
                                <button class="px-3 py-2 text-xs bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700" type="submit">
                                    Verzending mailen
                                </button>
                            </form>
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-green-700">
                                Bekijken →
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="md:hidden space-y-3">
    @foreach($orders as $order)
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-gray-500">Bestelling #{{ $order->id }}</p>
                    <p class="font-semibold leading-tight">{{ $order->name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $order->province ?? 'n.v.t.' }}</p>
                </div>
                <div class="text-right text-sm text-gray-600">
                    <p>{{ $order->created_at->format('d-m-Y') }}</p>
                    @if($order->route_date)
                        <p class="text-xs text-gray-500">Route: {{ $order->route_date->format('d-m-Y') }} @if($order->route_sequence)<span>(#{{ $order->route_sequence }})</span>@endif</p>
                    @else
                        <p class="text-xs text-gray-500">Nog niet gepland</p>
                    @endif
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <span class="text-lg font-semibold text-green-700">€ {{ number_format($order->total, 2, ',', '.') }}</span>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.orders.ship', $order) }}">
                        @csrf
                        <button class="inline-flex items-center justify-center rounded-full bg-green-600 px-4 py-2 text-white text-xs font-semibold" type="submit">
                            Verzending mailen
                        </button>
                    </form>
                    <a
                        href="{{ route('admin.orders.show', $order) }}"
                        class="inline-flex items-center justify-center rounded-full border border-green-600 px-4 py-2 text-green-700 text-xs font-semibold"
                    >
                        Openen
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4">{{ $orders->links() }}</div>
@endsection

<div id="bulk-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" data-close-bulk></div>
    <div class="absolute inset-x-0 top-24 mx-auto w-[min(92vw,520px)] rounded-2xl bg-white shadow-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold">Bulk naar route</h2>
            <button type="button" class="text-gray-500" data-close-bulk>✕</button>
        </div>

        <form method="POST" action="{{ route('admin.routes.bulk-create') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="route_date_filter" value="{{ $filters['route_date'] ?? '' }}">
            <input type="hidden" name="order_date_filter" value="{{ $filters['order_date'] ?? '' }}">
            <input type="hidden" name="province_filter" value="{{ $filters['province'] ?? '' }}">
            <input type="hidden" name="only_planned_filter" value="{{ request('only_planned') ? '1' : '' }}">
            <p class="text-xs text-gray-500">Deze bulkactie gebruikt alle bestellingen die bij de huidige filters horen, niet alleen deze pagina.</p>

            <div class="space-y-1">
                <label class="block text-sm text-gray-600">Route datum rijden</label>
                <input
                    type="date"
                    name="route_date"
                    value="{{ $filters['route_date'] ?? now()->toDateString() }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base"
                >
            </div>

            <div class="space-y-1">
                <label class="block text-sm text-gray-600">Chauffeur</label>
                <select name="admin_user_id" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
                    <option value="">Geen toewijzing</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">
                            {{ $admin->name }} ({{ $admin->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" data-close-bulk class="px-4 py-2 border rounded-lg text-gray-700 font-semibold">Annuleren</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold">Aanmaken</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-apply filters (date/province/only_planned) without a submit button
    (function() {
        const form = document.getElementById('order-filter-form');
        if (!form) return;
        const inputs = form.querySelectorAll('input[name="route_date"], select[name="province"], input[name="only_planned"]');

        const submitForm = () => form.requestSubmit ? form.requestSubmit() : form.submit();

        inputs.forEach((el) => {
            el.addEventListener('change', submitForm);
            el.addEventListener('input', (e) => {
                if (e.target.tagName === 'INPUT') submitForm();
            });
        });
    })();

    (function() {
        const modal = document.getElementById('bulk-modal');
        if (!modal) return;
        const openBtn = document.querySelector('[data-open-bulk]');
        const closeBtns = modal.querySelectorAll('[data-close-bulk]');

        const open = () => modal.classList.remove('hidden');
        const close = () => modal.classList.add('hidden');

        if (openBtn) openBtn.addEventListener('click', open);
        closeBtns.forEach(btn => btn.addEventListener('click', close));
    })();
</script>
@endpush
