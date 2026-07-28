@extends('admin.layouts.app')

@section('title', $mode === 'create' ? 'Nieuwe nieuwsbrief' : 'Nieuwsbrief bewerken')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
<script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
<style>
    trix-editor { min-height: 22rem; }
</style>
@endpush

@section('content')
@php
    $storedAudience = old('target_audience', $newsletter->target_audience === 'users' ? 'all_users' : ($newsletter->target_audience ?: 'all_users'));
    $storedFilters = $newsletter->filters ?? [];
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <a href="{{ route('admin.newsletters.index') }}" class="text-sm font-semibold text-gray-500">← Nieuwsbrieven</a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $mode === 'create' ? 'Nieuwe nieuwsbrief' : 'Nieuwsbrief bewerken' }}</h1>
        <p class="mt-1 text-sm text-gray-500">Bewerk de inhoud, kies de doelgroep en controleer de preview.</p>
    </div>
    @if($mode === 'edit')
        <form method="POST" action="{{ route('admin.newsletters.duplicate', $newsletter) }}">@csrf<button class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold">Dupliceren</button></form>
    @endif
</div>

@if(isset($errors) && $errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
@if(session('toast'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('toast') }}</div>@endif

<form
    id="newsletter-form"
    method="POST"
    action="{{ $mode === 'create' ? route('admin.newsletters.store') : route('admin.newsletters.update', $newsletter) }}"
    x-data="newsletterEditor()"
    class="space-y-6"
>
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-gray-900">Campagnegegevens</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="space-y-1 text-sm"><span class="font-medium">Interne titel</span><input type="text" name="title" value="{{ old('title', $newsletter->title) }}" required class="w-full rounded-xl border-gray-200"></label>
            <label class="space-y-1 text-sm"><span class="font-medium">Onderwerp van de e-mail</span><input type="text" name="subject" x-model="subject" value="{{ old('subject', $newsletter->subject) }}" required class="w-full rounded-xl border-gray-200"></label>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div><h2 class="font-semibold text-gray-900">Doelgroep</h2><p class="mt-1 text-xs text-gray-500">Uitgeschreven e-mailadressen worden altijd automatisch uitgesloten.</p></div>
            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-sm font-bold text-blue-700"><span x-text="audienceCount">{{ $audienceCount }}</span> ontvangers</span>
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="space-y-1 text-sm">
                <span class="font-medium">Wie ontvangt deze nieuwsbrief?</span>
                <select name="target_audience" x-model="audience" @change="updateAudienceCount" class="w-full rounded-xl border-gray-200">
                    <option value="all_users">Alle geregistreerde gebruikers</option>
                    <option value="customers">Alle klanten met een bestelling</option>
                    <option value="province">Klanten uit een provincie</option>
                    <option value="fulfillment">Klanten op ontvangstmethode</option>
                    <option value="recent_customers">Klanten sinds een datum</option>
                </select>
            </label>
            <label x-show="audience === 'province'" x-cloak class="space-y-1 text-sm">
                <span class="font-medium">Provincie</span>
                <select name="audience_province" x-model="province" @change="updateAudienceCount" class="w-full rounded-xl border-gray-200">
                    <option value="">Kies een provincie</option>
                    @foreach($provinces as $province)<option value="{{ $province }}">{{ $province }}</option>@endforeach
                </select>
            </label>
            <label x-show="audience === 'fulfillment'" x-cloak class="space-y-1 text-sm">
                <span class="font-medium">Ontvangstmethode</span>
                <select name="audience_fulfillment" x-model="fulfillment" @change="updateAudienceCount" class="w-full rounded-xl border-gray-200">
                    <option value="delivery">Thuisbezorgen</option>
                    <option value="pickup">Afhalen</option>
                </select>
            </label>
            <label x-show="audience === 'recent_customers'" x-cloak class="space-y-1 text-sm">
                <span class="font-medium">Besteld sinds</span>
                <input type="date" name="audience_ordered_since" x-model="orderedSince" @change="updateAudienceCount" class="w-full rounded-xl border-gray-200">
            </label>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(22rem,0.9fr)]">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div><h2 class="font-semibold text-gray-900">Inhoud</h2><p class="text-xs text-gray-500">Gebruik het menu om personalisatie toe te voegen.</p></div>
                <div class="flex flex-wrap gap-1">
                    @foreach(['{voornaam}' => 'Voornaam', '{naam}' => 'Naam', '{email}' => 'E-mail'] as $placeholder => $label)
                        <button type="button" @click="insertPlaceholder(@js($placeholder))" class="rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-semibold text-gray-600">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <input id="content_html" type="hidden" name="content_html" value="{{ old('content_html', $newsletter->content_html) }}">
            <trix-editor input="content_html" class="trix-content mt-4 rounded-xl border border-gray-200 bg-white"></trix-editor>
            <details class="mt-4 border-t border-gray-100 pt-4">
                <summary class="cursor-pointer text-sm font-semibold text-gray-600">Plain-textversie aanpassen</summary>
                <textarea name="content_text" class="mt-3 w-full rounded-xl border-gray-200 text-sm" rows="5" placeholder="Wordt automatisch afgeleid wanneer je dit leeg laat.">{{ old('content_text', $newsletter->content_text) }}</textarea>
            </details>
        </section>

        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-gray-100 shadow-sm xl:sticky xl:top-6 xl:self-start">
            <div class="border-b border-gray-200 bg-white px-5 py-4"><h2 class="font-semibold">Live preview</h2><p class="text-xs text-gray-500">Voorbeeld met testpersonalisatie.</p></div>
            <div class="p-4">
                <div class="mx-auto max-w-xl overflow-hidden rounded-xl bg-white shadow-sm">
                    <div class="bg-turbo-navy px-6 py-5 text-white"><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-turbo-gold">Kachels & vloeistoffen</p><strong class="mt-1 block">Kachelvloeistof.nl</strong></div>
                    <div class="border-b px-6 py-3 text-xs text-gray-500">Onderwerp: <strong class="text-gray-800" x-text="subject || 'Onderwerp nieuwsbrief'"></strong></div>
                    <div id="newsletter-preview" class="prose prose-sm min-h-56 max-w-none px-6 py-6"></div>
                    <div class="bg-turbo-navy px-6 py-4 text-xs text-white/60">Kachelvloeistof.nl · Uitschrijven</div>
                </div>
            </div>
        </section>
    </div>

    <div class="sticky bottom-4 flex justify-end">
        <button type="submit" class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-lg">{{ $mode === 'create' ? 'Concept aanmaken' : 'Wijzigingen opslaan' }}</button>
    </div>
</form>

@if($mode === 'edit')
    <section class="mt-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-gray-900">Testen en verzenden</h2>
        <div class="mt-4 grid gap-5 lg:grid-cols-3">
            <form method="POST" action="{{ route('admin.newsletters.test', $newsletter) }}" class="space-y-3 rounded-xl border border-gray-100 p-4">
                @csrf
                <div><strong class="text-sm">1. Testmail</strong><p class="text-xs text-gray-500">Controleer de nieuwsbrief in je eigen mailbox.</p></div>
                <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full rounded-lg border-gray-200 text-sm">
                <button class="w-full rounded-lg bg-gray-800 px-4 py-2.5 text-sm font-semibold text-white">Testmail versturen</button>
            </form>
            <form method="POST" action="{{ route('admin.newsletters.schedule', $newsletter) }}" class="space-y-3 rounded-xl border border-gray-100 p-4">
                @csrf
                <div><strong class="text-sm">2. Inplannen</strong><p class="text-xs text-gray-500">Nederlandse datum en tijd.</p></div>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($newsletter->scheduled_at)->timezone(config('newsletter.timezone'))?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border-gray-200 text-sm">
                <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">Verzending inplannen</button>
            </form>
            @if(in_array($newsletter->status, [\App\Models\Newsletter::STATUS_DRAFT, \App\Models\Newsletter::STATUS_SCHEDULED]))
                <form method="POST" action="{{ route('admin.newsletters.send', $newsletter) }}" class="space-y-3 rounded-xl border border-red-100 bg-red-50/40 p-4" onsubmit="return confirm('Definitief verzenden naar {{ $audienceCount }} ontvangers?')">
                    @csrf
                    <div><strong class="text-sm">3. Direct verzenden</strong><p class="text-xs text-gray-500">Doelgroep: {{ $audienceCount }} ontvangers.</p></div>
                    <label class="flex items-start gap-2 text-xs text-gray-700"><input type="checkbox" name="confirm_send" value="1" required class="mt-0.5 rounded"><span>Ik heb inhoud, links en doelgroep gecontroleerd.</span></label>
                    <button class="w-full rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white">Definitief verzenden</button>
                </form>
            @endif
        </div>
    </section>
@endif
@endsection

@push('scripts')
<script>
function newsletterEditor() {
    return {
        subject: @js(old('subject', $newsletter->subject)),
        audience: @js($storedAudience),
        province: @js(old('audience_province', $storedFilters['province'] ?? '')),
        fulfillment: @js(old('audience_fulfillment', $storedFilters['fulfillment_method'] ?? 'delivery')),
        orderedSince: @js(old('audience_ordered_since', $storedFilters['ordered_since'] ?? now()->subYear()->toDateString())),
        audienceCount: {{ $audienceCount }},
        init() {
            const input = document.getElementById('content_html');
            const preview = document.getElementById('newsletter-preview');
            const refresh = () => {
                preview.innerHTML = (input.value || '<p>Begin met het schrijven van je nieuwsbrief…</p>')
                    .replaceAll('{voornaam}', 'Jan')
                    .replaceAll('{naam}', 'Jan Jansen')
                    .replaceAll('{email}', 'jan@example.nl')
                    .replaceAll('{unsubscribe_url}', '#');
            };
            document.addEventListener('trix-change', refresh);
            refresh();
            this.updateAudienceCount();
        },
        insertPlaceholder(value) {
            document.querySelector('trix-editor')?.editor.insertString(value);
        },
        async updateAudienceCount() {
            const params = new URLSearchParams({
                target_audience: this.audience,
                province: this.province,
                fulfillment_method: this.fulfillment,
                ordered_since: this.orderedSince,
            });
            const response = await fetch(@js(route('admin.newsletters.audience-count')) + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) this.audienceCount = (await response.json()).count;
        }
    };
}
</script>
@endpush
