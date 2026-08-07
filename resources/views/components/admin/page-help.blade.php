@props(['title', 'intro', 'steps' => [], 'impact' => null])

<section class="mb-6 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-white p-4 shadow-sm"
         x-data="{ helpOpen: false }">
    <div class="flex items-start gap-3">
        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700" aria-hidden="true">?</span>
        <div class="min-w-0 flex-1">
            <h2 class="font-semibold text-gray-900">{{ $title }}</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">{{ $intro }}</p>
            @if($impact)
                <p class="mt-1 text-xs font-medium text-blue-800">Gevolg op de website: {{ $impact }}</p>
            @endif
        </div>
        <button type="button" @click="helpOpen = true" class="shrink-0 rounded-xl border border-blue-200 bg-white px-3 py-2 text-xs font-semibold text-blue-800 hover:bg-blue-50" aria-label="Open uitleg over deze pagina">
            Hoe werkt dit?
        </button>
    </div>

    <div x-cloak x-show="helpOpen" @keydown.escape.window="helpOpen = false" class="fixed inset-0 z-50" role="dialog" aria-modal="true" aria-label="Uitleg over deze pagina">
        <button type="button" class="absolute inset-0 bg-gray-950/40" @click="helpOpen = false" aria-label="Uitleg sluiten"></button>
        <aside class="absolute inset-y-0 right-0 w-full max-w-md overflow-y-auto bg-white p-6 shadow-2xl" x-transition>
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-xs font-bold uppercase tracking-wider text-blue-700">Uitleg</p><h2 class="mt-1 text-xl font-bold text-gray-900">{{ $title }}</h2></div>
                <button type="button" @click="helpOpen = false" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Sluiten">✕</button>
            </div>
            <p class="mt-5 text-sm leading-6 text-gray-600">{{ $intro }}</p>
            @if(count($steps))
                <ol class="mt-6 space-y-4">
                    @foreach($steps as $step)
                        <li class="flex gap-3 text-sm leading-6 text-gray-700"><span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">{{ $loop->iteration }}</span><span>{{ $step }}</span></li>
                    @endforeach
                </ol>
            @endif
            @if($impact)
                <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900"><strong>Let op:</strong> {{ $impact }}</div>
            @endif
            <a href="{{ route('admin.help') }}" class="mt-6 inline-flex rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Naar de volledige handleiding</a>
        </aside>
    </div>
</section>
