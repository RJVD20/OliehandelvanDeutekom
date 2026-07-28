@extends('admin.layouts.app')

@section('title', 'Auditlog')

@section('content')
@php
    $actionLabels = [
        'created' => 'Aangemaakt',
        'updated' => 'Gewijzigd',
        'deleted' => 'Verwijderd',
    ];
    $subjectLabels = [
        'product' => 'Product',
        'cms' => 'CMS',
        'location' => 'Locatie',
        'payment' => 'Betaling',
        'newsletter' => 'Nieuwsbrief',
    ];
    $formatValue = function ($value) {
        if (is_bool($value)) return $value ? 'Ja' : 'Nee';
        if ($value === null || $value === '') return 'Leeg';
        if (is_array($value)) return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return (string) $value;
    };
@endphp

<div class="mb-6">
    <p class="text-xs font-bold uppercase tracking-[0.18em] text-green-700">Beheercontrole</p>
    <h1 class="mt-1 text-2xl font-bold text-gray-800">Auditlog</h1>
    <p class="mt-1 text-sm text-gray-500">Bekijk wie CMS-inhoud en producten heeft aangepast.</p>
</div>

<form method="GET" action="{{ route('admin.audit.index') }}" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Zoek onderwerp of waarde" class="rounded-lg border-gray-200 text-sm xl:col-span-2">
        <select name="user_id" class="rounded-lg border-gray-200 text-sm">
            <option value="">Alle beheerders</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        <select name="subject_type" class="rounded-lg border-gray-200 text-sm">
            <option value="">Alle onderdelen</option>
            @foreach($subjectTypes as $type)
                <option value="{{ $type }}" @selected(request('subject_type') === $type)>{{ $subjectLabels[$type] ?? ucfirst($type) }}</option>
            @endforeach
        </select>
        <select name="action" class="rounded-lg border-gray-200 text-sm">
            <option value="">Alle acties</option>
            @foreach($actionLabels as $value => $label)
                <option value="{{ $value }}" @selected(request('action') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Filteren</button>
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-3">
        <label class="text-xs text-gray-500">Van <input type="date" name="date_from" value="{{ request('date_from') }}" class="ml-1 rounded-lg border-gray-200 text-sm"></label>
        <label class="text-xs text-gray-500">Tot <input type="date" name="date_to" value="{{ request('date_to') }}" class="ml-1 rounded-lg border-gray-200 text-sm"></label>
        @if(request()->hasAny(['search', 'user_id', 'subject_type', 'action', 'date_from', 'date_to']))
            <a href="{{ route('admin.audit.index') }}" class="text-sm text-gray-500 hover:text-gray-800">× Filters wissen</a>
        @endif
    </div>
</form>

<div class="space-y-3">
    @forelse($logs as $log)
        <details class="group overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <summary class="grid cursor-pointer list-none gap-3 p-4 sm:grid-cols-[10rem_1fr_auto] sm:items-center">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $log->created_at->format('d-m-Y H:i') }}</p>
                    <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-800">
                        {{ $log->user?->name ?? 'Onbekende gebruiker' }}
                        <span class="font-normal text-gray-500">{{ strtolower($actionLabels[$log->action] ?? $log->action) }}</span>
                        {{ strtolower($subjectLabels[$log->subject_type] ?? $log->subject_type) }}
                    </p>
                    <p class="truncate text-sm text-gray-500">{{ $log->subject_label ?: 'Zonder titel' }}</p>
                </div>
                <span class="inline-flex items-center gap-2 text-xs font-semibold text-green-700">
                    {{ count($log->changes ?? []) }} {{ count($log->changes ?? []) === 1 ? 'wijziging' : 'wijzigingen' }}
                    <span class="transition group-open:rotate-180">⌄</span>
                </span>
            </summary>

            <div class="border-t border-gray-100 bg-gray-50 p-4">
                @if($log->changes)
                    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Veld</th>
                                    <th class="px-4 py-3">Oude waarde</th>
                                    <th class="px-4 py-3">Nieuwe waarde</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($log->changes as $field => $change)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-gray-600">{{ $field }}</td>
                                        <td class="max-w-md whitespace-pre-wrap break-words px-4 py-3 text-red-700">{{ $formatValue($change['old'] ?? null) }}</td>
                                        <td class="max-w-md whitespace-pre-wrap break-words px-4 py-3 text-green-700">{{ $formatValue($change['new'] ?? null) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Voor deze actie zijn geen veldwijzigingen opgeslagen.</p>
                @endif
                <p class="mt-3 text-xs text-gray-400">IP: {{ $log->ip_address ?: 'onbekend' }}</p>
            </div>
        </details>
    @empty
        <div class="rounded-xl border border-dashed border-gray-200 bg-white p-10 text-center text-sm text-gray-500">
            Er zijn nog geen auditregels die aan deze filters voldoen.
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $logs->links() }}</div>
@endsection
