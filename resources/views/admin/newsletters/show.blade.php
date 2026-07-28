@extends('admin.layouts.app')

@section('title', $newsletter->title)

@section('content')
@php
    $statusLabels = [
        'draft' => ['Concept', 'bg-gray-100 text-gray-700'],
        'scheduled' => ['Ingepland', 'bg-indigo-100 text-indigo-700'],
        'sending' => ['Wordt verzonden', 'bg-blue-100 text-blue-700'],
        'sent' => ['Verzonden', 'bg-emerald-100 text-emerald-700'],
        'failed' => ['Met fouten', 'bg-red-100 text-red-700'],
    ];
    [$statusLabel, $statusClasses] = $statusLabels[$newsletter->status] ?? [ucfirst($newsletter->status), 'bg-gray-100 text-gray-700'];
    $attempted = $newsletter->sent_count + $newsletter->failed_count;
    $successRate = $attempted ? round(($newsletter->sent_count / $attempted) * 100, 1) : 0;
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <a href="{{ route('admin.newsletters.index') }}" class="text-sm font-semibold text-gray-500">← Nieuwsbrieven</a>
        <div class="mt-2 flex flex-wrap items-center gap-2">
            <h1 class="text-2xl font-bold text-gray-900">{{ $newsletter->title }}</h1>
            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
        </div>
        <p class="mt-1 text-sm text-gray-500">{{ $newsletter->subject }}</p>
    </div>
    @if(in_array($newsletter->status, [\App\Models\Newsletter::STATUS_DRAFT, \App\Models\Newsletter::STATUS_SCHEDULED]))
        <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white">Bewerken</a>
    @endif
</div>

<section class="mb-6 grid grid-cols-2 gap-3 xl:grid-cols-4">
    @foreach([
        ['Verwachte doelgroep', $audienceCount, 'bg-blue-50 text-blue-700'],
        ['Succesvol verzonden', $newsletter->sent_count, 'bg-emerald-50 text-emerald-700'],
        ['Mislukt', $newsletter->failed_count, 'bg-red-50 text-red-700'],
        ['Slagingspercentage', $successRate.'%', 'bg-purple-50 text-purple-700'],
    ] as [$label, $value, $colors])
        <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500">{{ $label }}</p>
            <strong class="mt-2 block text-2xl {{ $colors }} rounded-lg bg-transparent">{{ $value }}</strong>
        </article>
    @endforeach
</section>

<div class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)]">
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-5 py-4"><h2 class="font-semibold">Nieuwsbriefpreview</h2></div>
        <div class="bg-gray-100 p-4 sm:p-6">
            <div class="mx-auto max-w-2xl overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="bg-turbo-navy px-7 py-6 text-white"><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-turbo-gold">Kachels & vloeistoffen</p><strong class="mt-1 block text-lg">Kachelvloeistof.nl</strong></div>
                <div class="prose max-w-none px-7 py-7">{!! $newsletter->content_html !!}</div>
                <div class="bg-turbo-navy px-7 py-5 text-xs text-white/60">Kachelvloeistof.nl · Uitschrijven</div>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Verzendgegevens</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-gray-500">Gepland</dt><dd class="text-right font-semibold">{{ $newsletter->scheduled_at?->timezone(config('newsletter.timezone'))->format('d-m-Y H:i') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-500">Verzonden op</dt><dd class="text-right font-semibold">{{ $newsletter->sent_at?->timezone(config('newsletter.timezone'))->format('d-m-Y H:i') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-500">Nog in wachtrij</dt><dd class="font-semibold">{{ $newsletter->queued_count }}</dd></div>
            </dl>
        </section>
        @if($newsletter->failed_count)
            <section class="rounded-2xl border border-red-100 bg-red-50 p-5">
                <h2 class="font-semibold text-red-900">Verzendfouten</h2>
                <p class="mt-1 text-sm text-red-700">{{ $newsletter->failed_count }} ontvangers konden niet worden bereikt. Bekijk hieronder de foutredenen.</p>
            </section>
        @endif
    </aside>
</div>

<section class="mt-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <div><h2 class="font-semibold">Ontvangers en resultaten</h2><p class="text-xs text-gray-500">{{ $sends->total() }} verzendrecords</p></div>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($sends as $send)
            <div class="grid gap-2 px-5 py-3 text-sm sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                <div class="min-w-0"><strong class="block truncate">{{ $send->recipient_name ?: $send->recipient_email }}</strong><span class="block truncate text-xs text-gray-500">{{ $send->recipient_email }}</span></div>
                <div class="text-left sm:text-right">
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $send->status === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($send->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">{{ ucfirst($send->status) }}</span>
                    @if($send->failure_reason)<p class="mt-1 max-w-md text-xs text-red-600">{{ $send->failure_reason }}</p>@endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-sm text-gray-500">Er zijn nog geen verzendrecords.</div>
        @endforelse
    </div>
    <div class="border-t px-5 py-4">{{ $sends->links() }}</div>
</section>
@endsection
