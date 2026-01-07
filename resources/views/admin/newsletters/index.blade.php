@extends('admin.layouts.app')

@section('title', 'Nieuwsbrieven')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Nieuwsbrieven</h1>
        <a href="{{ route('admin.newsletters.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Nieuwe nieuwsbrief</a>
    </div>

    <div class="bg-white shadow rounded-lg divide-y">
        @forelse($newsletters as $newsletter)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">{{ $newsletter->created_at->format('d-m-Y H:i') }}</div>
                    <div class="font-semibold">{{ $newsletter->title }}</div>
                    <div class="text-sm text-gray-600">Subject: {{ $newsletter->subject }}</div>
                    <div class="text-xs mt-1 text-gray-500">
                        Status: <span class="font-medium">{{ $newsletter->status }}</span>
                        @if($newsletter->scheduled_at)
                            · Gepland: {{ $newsletter->scheduled_at->format('d-m-Y H:i') }}
                        @endif
                        · Verzonden: {{ $newsletter->sent_count }}
                        · Fouten: {{ $newsletter->failed_count }}
                    </div>
                </div>
                <div class="flex gap-2 text-sm">
                    <a href="{{ route('admin.newsletters.show', $newsletter) }}" class="px-3 py-2 bg-gray-100 rounded">Bekijken</a>
                    <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="px-3 py-2 bg-blue-50 text-blue-700 rounded">Bewerken</a>
                    @if($newsletter->status === \App\Models\Newsletter::STATUS_SCHEDULED)
                        <form method="POST" action="{{ route('admin.newsletters.cancel', $newsletter) }}">
                            @csrf
                            <button class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded" onclick="return confirm('Geplande verzending annuleren?')">Annuleer</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6 text-gray-500">Geen nieuwsbrieven gevonden.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $newsletters->links() }}</div>
@endsection
