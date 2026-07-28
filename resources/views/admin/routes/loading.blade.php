@extends('admin.layouts.app')

@section('title', 'Wagen laden – '.$deliveryRoute->name)

@push('head')
<style>
    .paper-check { display: none; }

    @media print {
        @page { size: A4 portrait; margin: 12mm; }
        body { background: #fff !important; color: #000 !important; }
        aside,
        .turbo-admin-mobilebar,
        .loading-screen-only,
        .whatsapp-fab { display: none !important; }
        main { margin-left: 0 !important; }
        .turbo-admin-content { padding: 0 !important; }
        .loading-print-header { display: block !important; }
        .loading-grid { display: block !important; }
        .loading-item {
            display: block !important;
            break-inside: avoid;
            margin-bottom: 5mm;
            border: 1.5px solid #111827 !important;
            border-radius: 2mm !important;
            box-shadow: none !important;
        }
        .loading-item.is-loaded { opacity: 1 !important; }
        .loading-item-image { width: 22mm !important; height: 22mm !important; }
        .loading-item-details { display: block !important; }
        .loading-item-details > div { display: grid !important; }
        .web-check { display: none !important; }
        .paper-check {
            display: inline-block !important;
            width: 8mm;
            height: 8mm;
            flex: 0 0 8mm;
            border: 2px solid #111827;
            border-radius: 1mm;
        }
        .print-muted { color: #374151 !important; }
    }
</style>
@endpush

@section('content')
<div
    x-data="loadingChecklist({
        toggleUrl: @js(route('admin.routes.loading.toggle', $deliveryRoute)),
        checkedItems: @js($checkedItems->all()),
        totalCount: {{ $inventory->count() }},
        totalQuantity: {{ $totalQuantity }},
        initialLoadedQuantity: {{ $loadedQuantity }},
    })"
>
    <div class="loading-screen-only mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a
                href="{{ route('admin.routes.index', ['route_date' => $deliveryRoute->route_date->toDateString(), 'route_id' => $deliveryRoute->id]) }}"
                class="text-sm font-semibold text-turbo-blue hover:underline"
            >
                ← Terug naar route
            </a>
            <p class="mt-5 text-xs font-bold uppercase tracking-[0.18em] text-turbo-gold">Laadlijst</p>
            <h1 class="mt-1 text-2xl font-bold">Wagen laden</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $deliveryRoute->name }} · {{ $deliveryRoute->route_date->format('d-m-Y') }}</p>
        </div>
        <button
            type="button"
            onclick="window.print()"
            class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
        >
            Print paklijst
        </button>
    </div>

    <header class="loading-print-header mb-6 hidden border-b-2 border-gray-900 pb-4">
        <div class="flex items-start justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold">Paklijst – {{ $deliveryRoute->name }}</h1>
                <p class="mt-1 text-sm">
                    Bezorgdatum: {{ $deliveryRoute->route_date->format('d-m-Y') }}
                    · Chauffeur: {{ $deliveryRoute->admin?->name ?? 'Niet toegewezen' }}
                </p>
            </div>
            <div class="text-right text-sm">
                <strong>{{ $inventory->count() }} productsoorten</strong><br>
                {{ $totalQuantity }} stuks totaal
            </div>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-8 text-sm">
            <span>Ingeladen door: ____________________</span>
            <span>Controle: ____________________</span>
        </div>
    </header>

    @if($inventory->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500">
            Deze route bevat nog geen producten om in te laden.
        </div>
    @else
        <section class="loading-screen-only mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-600">Laadvoortgang</span>
                    <div class="mt-1 flex items-baseline gap-2">
                        <strong class="text-2xl text-gray-900" x-text="`${checkedCount} / ${totalCount}`"></strong>
                        <span class="text-sm text-gray-500">productsoorten</span>
                        <span class="text-sm text-gray-400">·</span>
                        <span class="text-sm text-gray-500" x-text="`${loadedQuantity} / ${totalQuantity} stuks`"></span>
                    </div>
                </div>
                <div class="flex rounded-xl bg-gray-100 p-1 text-sm font-semibold">
                    <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'" class="rounded-lg px-3 py-2">Alles</button>
                    <button type="button" @click="filter = 'open'" :class="filter === 'open' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'" class="rounded-lg px-3 py-2">Nog laden</button>
                    <button type="button" @click="filter = 'loaded'" :class="filter === 'loaded' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'" class="rounded-lg px-3 py-2">Geladen</button>
                </div>
            </div>
            <div class="mt-4 h-3 overflow-hidden rounded-full bg-gray-100">
                <div
                    class="h-full rounded-full bg-green-600 transition-all"
                    :style="`width: ${totalCount ? (checkedCount / totalCount) * 100 : 0}%`"
                ></div>
            </div>
            <p
                x-show="checkedCount === totalCount"
                x-cloak
                class="mt-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-800"
            >
                Alles voor deze route is ingeladen.
            </p>
        </section>

        <div class="loading-grid grid gap-4 lg:grid-cols-2">
            @foreach($inventory as $item)
                <article
                    x-show="filter === 'all' || (filter === 'loaded') === isChecked(@js($item['key']))"
                    :class="{ 'is-loaded border-green-300 bg-green-50/60': isChecked(@js($item['key'])), 'opacity-60': saving === @js($item['key']) }"
                    class="loading-item overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition"
                >
                    <div class="flex gap-4 p-4">
                        <span class="paper-check mt-1" aria-hidden="true"></span>

                        <div class="loading-item-image flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
                            @if($item['image'])
                                <img
                                    src="{{ asset('storage/'.$item['image']) }}"
                                    alt="{{ $item['name'] }}"
                                    class="h-full w-full object-contain p-2"
                                    loading="lazy"
                                >
                            @else
                                <span class="px-2 text-center text-xs text-gray-400">Geen foto</span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-semibold text-gray-900">{{ $item['name'] }}</h2>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $item['stops']->count() }} {{ $item['stops']->count() === 1 ? 'stop' : 'stops' }}
                                    </p>
                                </div>
                                <strong class="shrink-0 rounded-xl bg-turbo-navy px-3 py-2 text-lg text-white">
                                    {{ $item['quantity'] }}×
                                </strong>
                            </div>

                            <button
                                type="button"
                                @click="toggle(@js($item['key']), {{ $item['quantity'] }})"
                                :disabled="saving === @js($item['key'])"
                                :class="isChecked(@js($item['key'])) ? 'border-green-600 bg-green-600 text-white' : 'border-gray-300 bg-white text-gray-700'"
                                class="web-check mt-4 flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition disabled:cursor-wait"
                            >
                                <span x-text="isChecked(@js($item['key'])) ? '✓ Geladen' : 'Markeer als geladen'"></span>
                            </button>
                        </div>
                    </div>

                    <details class="loading-item-details border-t border-gray-100">
                        <summary class="loading-screen-only cursor-pointer px-4 py-3 text-xs font-semibold text-turbo-blue hover:bg-gray-50">
                            Verdeling over stops
                        </summary>
                        <div class="grid gap-x-6 gap-y-2 bg-gray-50 px-4 py-3 text-xs sm:grid-cols-2">
                            @foreach($item['stops'] as $stop)
                                <div class="flex items-baseline justify-between gap-3 border-b border-gray-200 py-1.5">
                                    <span class="min-w-0 truncate">
                                        <strong>Stop {{ $stop['sequence'] ?? '—' }}</strong>
                                        <span class="print-muted text-gray-500"> · #{{ $stop['order_id'] }} {{ $stop['customer'] }}</span>
                                    </span>
                                    <strong class="shrink-0">{{ $stop['quantity'] }}×</strong>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function loadingChecklist(config) {
        return {
            checkedItems: config.checkedItems,
            totalCount: config.totalCount,
            totalQuantity: config.totalQuantity,
            loadedQuantity: config.initialLoadedQuantity,
            filter: 'all',
            saving: null,

            get checkedCount() {
                return this.checkedItems.length;
            },

            isChecked(key) {
                return this.checkedItems.includes(key);
            },

            async toggle(key, quantity) {
                if (this.saving) return;

                const wasChecked = this.isChecked(key);
                this.saving = key;
                this.checkedItems = wasChecked
                    ? this.checkedItems.filter(item => item !== key)
                    : [...this.checkedItems, key];
                this.loadedQuantity += wasChecked ? -quantity : quantity;

                try {
                    const response = await fetch(config.toggleUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            item_key: key,
                            loaded: !wasChecked,
                        }),
                    });

                    if (!response.ok) throw new Error('Opslaan mislukt');

                    const result = await response.json();
                    this.checkedItems = result.checked_items;
                    this.loadedQuantity = result.loaded_quantity;
                } catch (error) {
                    this.checkedItems = wasChecked
                        ? [...this.checkedItems, key]
                        : this.checkedItems.filter(item => item !== key);
                    this.loadedQuantity += wasChecked ? quantity : -quantity;
                    window.alert('De laadstatus kon niet worden opgeslagen. Probeer het opnieuw.');
                } finally {
                    this.saving = null;
                }
            },
        };
    }
</script>
@endpush
