@extends('admin.layouts.app')

@section('title', $newsletter->title)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $newsletter->title }}</h1>
            <div class="text-sm text-gray-500">Status: {{ $newsletter->status }} · Verzonden: {{ $newsletter->sent_count }} · Fouten: {{ $newsletter->failed_count }}</div>
        </div>
        <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="px-3 py-2 bg-blue-600 text-white rounded">Bewerken</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Inhoud</h2>
            <div class="prose max-w-none">{!! $newsletter->content_html !!}</div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Statistieken</h2>
            <ul class="text-sm space-y-2">
                <li>Verzonden: {{ $newsletter->sent_count }}</li>
                <li>Fouten: {{ $newsletter->failed_count }}</li>
                <li>Gepland: {{ $newsletter->scheduled_at ?? '—' }}</li>
                <li>Verzonden op: {{ $newsletter->sent_at ?? '—' }}</li>
            </ul>
        </div>
    </div>

    <div class="mt-8 bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-semibold">Ontvangers</h2>
            <div class="text-sm text-gray-500">{{ $sends->total() }} records</div>
        </div>
        <div class="divide-y">
            @foreach($sends as $send)
                <div class="px-6 py-3 flex items-center justify-between text-sm">
                    <div>
                        <div class="font-medium">{{ $send->recipient_email }}</div>
                        <div class="text-gray-500">Status: {{ $send->status }}</div>
                    </div>
                    <div class="text-gray-500 text-xs">
                        @if($send->sent_at)
                            Verzonden: {{ $send->sent_at }}
                        @elseif($send->failed_at)
                            Fout: {{ $send->failure_reason }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-6 py-4">{{ $sends->links() }}</div>
    </div>
@endsection
