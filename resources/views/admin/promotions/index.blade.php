@extends('admin.layouts.app')
@section('title', 'Acties')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div><p class="text-xs font-bold uppercase tracking-wider text-gray-400">Webshop</p><h1 class="text-2xl font-bold">Acties</h1><p class="mt-1 text-sm text-gray-500">Plan bundels, actieprijzen en gratis verzending.</p></div>
    <a href="{{ route('admin.promotions.create') }}" class="rounded-xl bg-turbo-navy px-4 py-2.5 text-sm font-bold text-white">Nieuwe actie</a>
</div>
<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="divide-y divide-gray-100">
        @forelse($promotions as $promotion)
            @php
                $state = ! $promotion->active ? ['Concept', 'bg-gray-100 text-gray-600'] : ($promotion->isCurrentlyActive() ? ['Actief', 'bg-emerald-100 text-emerald-700'] : (($promotion->starts_at?->isFuture()) ? ['Gepland', 'bg-blue-100 text-blue-700'] : ['Afgelopen', 'bg-amber-100 text-amber-700']));
            @endphp
            <article class="flex flex-wrap items-center gap-4 p-4 sm:p-5">
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border bg-gray-50">
                    @if($promotion->imageUrl())<img src="{{ $promotion->imageUrl() }}" alt="" class="h-full w-full object-cover">@endif
                </div>
                <div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><h2 class="font-bold">{{ $promotion->name }}</h2><span class="rounded-full px-2 py-1 text-xs font-bold {{ $state[1] }}">{{ $state[0] }}</span></div><p class="mt-1 text-sm text-gray-500">{{ $promotion->mainProduct?->name }} · € {{ number_format($promotion->fixed_price, 2, ',', '.') }}</p><p class="mt-1 text-xs text-gray-400">{{ $promotion->starts_at?->format('d-m-Y H:i') ?: 'Direct' }} – {{ $promotion->ends_at?->format('d-m-Y H:i') ?: 'Geen einddatum' }}</p></div>
                <a href="{{ route('admin.promotions.edit', $promotion) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-bold text-gray-700">Bewerken</a>
            </article>
        @empty
            <div class="p-12 text-center text-gray-500">Nog geen acties aangemaakt.</div>
        @endforelse
    </div>
</section>
<div class="mt-6">{{ $promotions->links() }}</div>
@endsection
