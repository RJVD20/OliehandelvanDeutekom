@extends('admin.layouts.app')

@section('title', 'Nieuwsbrieven')

@section('content')
@php
    $statusLabels = [
        'draft' => ['Concept', 'bg-gray-100 text-gray-700'],
        'scheduled' => ['Ingepland', 'bg-indigo-100 text-indigo-700'],
        'sending' => ['Wordt verzonden', 'bg-blue-100 text-blue-700'],
        'sent' => ['Verzonden', 'bg-emerald-100 text-emerald-700'],
        'failed' => ['Met fouten', 'bg-red-100 text-red-700'],
    ];
    $audienceLabels = [
        'all_users' => 'Alle gebruikers',
        'users' => 'Alle gebruikers',
        'customers' => 'Alle klanten',
        'province' => 'Klanten per provincie',
        'fulfillment' => 'Klanten per ontvangstmethode',
        'recent_customers' => 'Recente klanten',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-green-700">E-mailcampagnes</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Nieuwsbrieven</h1>
        <p class="mt-1 text-sm text-gray-500">Maak, test, plan en volg nieuwsbrieven vanuit één overzicht.</p>
    </div>
    <a href="{{ route('admin.newsletters.create') }}" class="rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm">+ Nieuwe nieuwsbrief</a>
</div>

@if(session('toast'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('toast') }}</div>
@endif

<section class="mb-6 grid grid-cols-2 gap-3 xl:grid-cols-4">
    @foreach([
        ['Concepten', $stats['drafts'], 'draft', 'bg-gray-100 text-gray-700', 'C'],
        ['Ingepland', $stats['scheduled'], 'scheduled', 'bg-indigo-50 text-indigo-700', 'P'],
        ['Verzonden deze maand', $stats['sent_this_month'], 'sent', 'bg-emerald-50 text-emerald-700', '✓'],
        ['Bereikbare ontvangers', $stats['eligible_recipients'], null, 'bg-blue-50 text-blue-700', '@'],
    ] as [$label, $value, $status, $colors, $icon])
        <a href="{{ $status ? route('admin.newsletters.index', ['status' => $status]) : '#' }}" class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-xs text-gray-500 sm:text-sm">{{ $label }}</p><strong class="mt-2 block text-2xl text-gray-900">{{ $value }}</strong></div>
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-xs font-bold {{ $colors }}">{{ $icon }}</span>
            </div>
        </a>
    @endforeach
</section>

<form method="GET" class="mb-6 grid gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:grid-cols-[minmax(15rem,1fr)_12rem_auto]">
    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Zoek op titel of onderwerp" class="rounded-xl border-gray-200 text-sm">
    <select name="status" class="rounded-xl border-gray-200 text-sm">
        <option value="">Alle statussen</option>
        @foreach($statusLabels as $value => [$label])
            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <div class="flex gap-2">
        <button class="rounded-xl bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white">Filteren</button>
        @if(request()->hasAny(['search', 'status']))<a href="{{ route('admin.newsletters.index') }}" class="rounded-xl border px-4 py-2.5 text-sm font-semibold">Reset</a>@endif
    </div>
</form>

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-5 py-3"><h2 class="font-semibold">{{ $newsletters->total() }} nieuwsbrieven</h2></div>
    <div class="divide-y divide-gray-100">
        @forelse($newsletters as $newsletter)
            @php [$statusLabel, $statusClasses] = $statusLabels[$newsletter->status] ?? [ucfirst($newsletter->status), 'bg-gray-100 text-gray-700']; @endphp
            <article class="grid gap-4 p-4 hover:bg-gray-50 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center sm:px-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.newsletters.show', $newsletter) }}" class="truncate font-semibold text-gray-900 hover:text-green-700">{{ $newsletter->title }}</a>
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                    </div>
                    <p class="mt-1 truncate text-sm text-gray-600">{{ $newsletter->subject }}</p>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                        <span>{{ $audienceLabels[$newsletter->target_audience] ?? $newsletter->target_audience }}</span>
                        @if($newsletter->scheduled_at)<span>Gepland: {{ $newsletter->scheduled_at->timezone(config('newsletter.timezone'))->format('d-m-Y H:i') }}</span>@endif
                        <span>{{ $newsletter->sent_count }} verzonden</span>
                        @if($newsletter->failed_count)<span class="font-semibold text-red-600">{{ $newsletter->failed_count }} fouten</span>@endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.newsletters.show', $newsletter) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700">Resultaten</a>
                    @if(in_array($newsletter->status, [\App\Models\Newsletter::STATUS_DRAFT, \App\Models\Newsletter::STATUS_SCHEDULED]))
                        <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">Bewerken</a>
                    @endif
                    @if($newsletter->status === \App\Models\Newsletter::STATUS_SCHEDULED)
                        <form method="POST" action="{{ route('admin.newsletters.cancel', $newsletter) }}">@csrf<button class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700" onclick="return confirm('Geplande verzending annuleren?')">Annuleren</button></form>
                    @endif
                </div>
            </article>
        @empty
            <div class="p-10 text-center text-sm text-gray-500">Geen nieuwsbrieven gevonden.</div>
        @endforelse
    </div>
</section>

<div class="mt-6">{{ $newsletters->links() }}</div>
@endsection
