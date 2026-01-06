@extends('admin.layouts.app')

@section('title', 'Openstaande betalingen')

@section('content')
<h1 class="text-2xl font-bold mb-6">Betalingen</h1>

<div class="bg-white rounded shadow p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div class="space-y-1">
            <label class="text-sm text-gray-600">Status</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
                <option value="">Alle</option>
                @foreach(['open','paid','expired','failed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-1">
            <label class="text-sm text-gray-600">Vervalt vóór</label>
            <input type="date" name="due_before" value="{{ $filters['due_before'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
        </div>
        <div class="space-y-1">
            <label class="text-sm text-gray-600">Vervalt na</label>
            <input type="date" name="due_after" value="{{ $filters['due_after'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-3 text-base">
        </div>
        <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-sm font-medium text-gray-800">
            <input type="checkbox" name="soon" value="1" @checked(request('soon')) class="h-5 w-5 rounded border-gray-300">
            <span>Bijna vervallen (<=3d)</span>
        </label>
        <div class="lg:col-span-4 flex gap-3 justify-end">
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-3 border rounded-lg text-center font-semibold text-gray-800">Reset</a>
            <button class="px-4 py-3 bg-green-600 text-white rounded-lg font-semibold">Filter</button>
        </div>
    </form>
</div>

<div class="hidden md:block">
    <table class="w-full bg-white rounded shadow text-sm">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="p-3 text-left">Order</th>
                <th class="p-3 text-left">Klant</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Vervalt</th>
                <th class="p-3 text-left">Laatste herinnering</th>
                <th class="p-3 text-left">Teller</th>
                <th class="p-3 text-right">Bedrag</th>
                <th class="p-3 text-right">Acties</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">#{{ $payment->order_id }}</td>
                    <td class="p-3">
                        <div class="font-semibold">{{ $payment->order->name }}</div>
                        <div class="text-xs text-gray-500">{{ $payment->order->email }}</div>
                    </td>
                    <td class="p-3">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst($payment->status->value) }}</span>
                    </td>
                    <td class="p-3">{{ optional($payment->due_date)->format('d-m-Y') }}</td>
                    <td class="p-3">{{ optional($payment->last_reminder_at)->format('d-m-Y H:i') ?? '–' }}</td>
                    <td class="p-3">{{ $payment->reminder_count }}</td>
                    <td class="p-3 text-right">€ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                    <td class="p-3 text-right space-x-2">
                        @if($payment->status->value === 'open')
                            <form method="POST" action="{{ route('admin.payments.remind', $payment) }}" class="inline">
                                @csrf
                                <button class="px-3 py-2 rounded bg-blue-600 text-white text-xs font-semibold">Herinnering</button>
                            </form>
                            <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="px-3 py-2 rounded bg-green-600 text-white text-xs font-semibold">Markeer betaald</button>
                            </form>
                        @else
                            <span class="text-xs text-gray-500">Geen acties</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="md:hidden space-y-3">
    @foreach($payments as $payment)
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold">Order #{{ $payment->order_id }}</p>
                    <p class="text-xs text-gray-500">{{ $payment->order->name }} — {{ $payment->order->email }}</p>
                </div>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ ucfirst($payment->status->value) }}</span>
            </div>
            <div class="mt-2 text-sm text-gray-700 space-y-1">
                <div>Vervalt: {{ optional($payment->due_date)->format('d-m-Y') }}</div>
                <div>Laatste herinnering: {{ optional($payment->last_reminder_at)->format('d-m-Y H:i') ?? '–' }}</div>
                <div>Herinneringen: {{ $payment->reminder_count }}</div>
                <div class="font-semibold text-green-700">€ {{ number_format($payment->amount, 2, ',', '.') }}</div>
            </div>
            <div class="mt-3 flex flex-col sm:flex-row gap-2">
                @if($payment->status->value === 'open')
                    <form method="POST" action="{{ route('admin.payments.remind', $payment) }}">
                        @csrf
                        <button class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white text-sm font-semibold">Herinnering</button>
                    </form>
                    <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}">
                        @csrf
                        @method('PATCH')
                        <button class="w-full rounded-lg bg-green-600 px-4 py-2 text-white text-sm font-semibold">Markeer betaald</button>
                    </form>
                @else
                    <span class="text-xs text-gray-500">Geen acties</span>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4">{{ $payments->links() }}</div>
@endsection
