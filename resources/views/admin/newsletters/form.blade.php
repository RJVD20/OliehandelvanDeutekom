@extends('admin.layouts.app')

@section('title', $mode === 'create' ? 'Nieuwe nieuwsbrief' : 'Nieuwsbrief bewerken')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">{{ $mode === 'create' ? 'Nieuwe nieuwsbrief' : 'Nieuwsbrief bewerken' }}</h1>
        @if($mode === 'edit')
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.newsletters.duplicate', $newsletter) }}">
                    @csrf
                    <button class="px-3 py-2 bg-gray-100 rounded">Dupliceer</button>
                </form>
                @if(in_array($newsletter->status, [\App\Models\Newsletter::STATUS_DRAFT, \App\Models\Newsletter::STATUS_SCHEDULED]))
                    <form method="POST" action="{{ route('admin.newsletters.send', $newsletter) }}">
                        @csrf
                        <button class="px-3 py-2 bg-green-600 text-white rounded" onclick="return confirm('Direct verzenden?')">Verzend nu</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <form method="POST" action="{{ $mode === 'create' ? route('admin.newsletters.store') : route('admin.newsletters.update', $newsletter) }}">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-4">
                <label class="block">
                    <span class="text-sm font-medium">Titel</span>
                    <input type="text" name="title" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('title', $newsletter->title) }}" required>
                </label>

                <label class="block">
                    <span class="text-sm font-medium">Onderwerp</span>
                    <input type="text" name="subject" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('subject', $newsletter->subject) }}" required>
                </label>

                <label class="block">
                    <span class="text-sm font-medium">Inhoud (HTML, placeholders: {voornaam}, {email}, {unsubscribe_url})</span>
                    <input id="content_html" type="hidden" name="content_html" value="{{ old('content_html', $newsletter->content_html) }}">
                    <trix-editor input="content_html" class="trix-content bg-white border rounded"></trix-editor>
                </label>

                <label class="block">
                    <span class="text-sm font-medium">Plain text (optioneel)</span>
                    <textarea name="content_text" class="mt-1 w-full border rounded px-3 py-2" rows="4">{{ old('content_text', $newsletter->content_text) }}</textarea>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6">
                <div class="text-sm text-gray-500">
                    Huidige status: <span class="font-medium">{{ $newsletter->status }}</span>
                    @if($newsletter->scheduled_at)
                        · Gepland: {{ $newsletter->scheduled_at }}
                    @endif
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Opslaan als concept</button>
            </div>
        </form>

        @if($mode === 'edit')
            <div class="border-t pt-4 space-y-4">
                <div class="font-semibold">Plan verzending</div>
                <form method="POST" action="{{ route('admin.newsletters.schedule', $newsletter) }}" class="flex gap-2 items-end">
                    @csrf
                    <label class="flex-1">
                        <span class="text-sm font-medium">Datum & tijd</span>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($newsletter->scheduled_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full border rounded px-3 py-2" required>
                    </label>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Inplannen</button>
                </form>

                <div class="font-semibold">Testmail</div>
                <form method="POST" action="{{ route('admin.newsletters.test', $newsletter) }}" class="flex gap-2 items-end">
                    @csrf
                    <label class="flex-1">
                        <span class="text-sm font-medium">Test e-mail</span>
                        <input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" placeholder="test@example.com" required>
                    </label>
                    <button class="px-4 py-2 bg-gray-800 text-white rounded">Stuur test</button>
                </form>
            </div>
        @endif
    </div>
@endsection
