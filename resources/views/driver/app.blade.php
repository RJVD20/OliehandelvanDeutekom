@extends('driver.layouts.app')

@section('title', 'Route App')

@section('content')
<div class="app-fade-in">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[color:var(--app-muted)]">Chauffeur</p>
            <h1 class="text-2xl font-semibold">Welkom, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-[color:var(--app-muted)]">Routeplanning en stops van vandaag</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <div class="h-10 w-10 rounded-full bg-[color:var(--app-card-2)] border border-[color:var(--app-border)] flex items-center justify-center">
                <span class="text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-[1.2fr,0.8fr] gap-5">
        <div class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-card)] p-5 app-glow">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-[color:var(--app-muted)]">Route datum</p>
                    <p class="text-lg font-semibold">{{ \Carbon\Carbon::parse($routeDate)->translatedFormat('D d M Y') }}</p>
                </div>
                <form method="GET" action="{{ route('driver.app') }}" class="flex items-center gap-2">
                    <input
                        type="date"
                        name="route_date"
                        value="{{ $routeDate }}"
                        class="driver-date rounded-lg border border-[color:var(--app-border)] px-3 py-2 text-sm [color-scheme:dark]"
                    >
                    <button class="px-4 py-2 rounded-lg bg-white/10 text-white text-sm font-semibold">Ga</button>
                </form>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="rounded-xl border border-[color:var(--app-border)] bg-[color:var(--app-card-2)] p-4">
                    <p class="text-xs text-[color:var(--app-muted)]">Stops</p>
                    <p class="text-2xl font-semibold">{{ $orders->count() }}</p>
                </div>
                <div class="rounded-xl border border-[color:var(--app-border)] bg-[color:var(--app-card-2)] p-4">
                    <p class="text-xs text-[color:var(--app-muted)]">Afgerond</p>
                    <p class="text-2xl font-semibold">{{ $orders->where('status', 'completed')->count() }}</p>
                </div>
                <div class="rounded-xl border border-[color:var(--app-border)] bg-[color:var(--app-card-2)] p-4">
                    <p class="text-xs text-[color:var(--app-muted)]">Open</p>
                    <p class="text-2xl font-semibold">{{ $orders->where('status', '!=', 'completed')->count() }}</p>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <a
                    href="{{ $routeMapUrl ?? '#' }}"
                    class="inline-flex items-center justify-center rounded-lg bg-[color:var(--app-accent)] text-black px-5 py-3 text-sm font-semibold {{ $routeMapUrl ? '' : 'pointer-events-none opacity-40' }}"
                >
                    Start route in Google Maps
                </a>
                <a
                    href="{{ route('admin.routes.index', ['route_date' => $routeDate]) }}"
                    class="inline-flex items-center justify-center rounded-lg border border-[color:var(--app-border)] px-5 py-3 text-sm font-semibold text-white/90"
                >
                    Routeplanning openen
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-[color:var(--app-border)] bg-[color:var(--app-card)] p-5">
            <p class="text-xs uppercase tracking-[0.2em] text-[color:var(--app-muted)]">Status</p>
            <h2 class="text-lg font-semibold mt-1">Vandaag</h2>
            <div class="mt-4 space-y-3">
                @if($orders->isEmpty())
                    <div class="rounded-xl border border-dashed border-[color:var(--app-border)] p-4 text-sm text-[color:var(--app-muted)]">
                        Geen stops ingepland voor deze datum.
                    </div>
                @else
                    @foreach($orders as $order)
                        <div class="rounded-xl border border-[color:var(--app-border)] bg-[color:var(--app-card-2)] p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs text-[color:var(--app-muted)]">Stop {{ $order->route_sequence ?? '—' }}</p>
                                    <p class="font-semibold">#{{ $order->id }} {{ $order->name }}</p>
                                    <p class="text-sm text-[color:var(--app-muted)]">{{ $order->address }}, {{ $order->postcode }} {{ $order->city }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full border border-[color:var(--app-border)] {{ $order->status === 'completed' ? 'text-[color:var(--app-accent)]' : 'text-white/70' }}">
                                    {{ $order->status === 'completed' ? 'Afgerond' : 'Open' }}
                                </span>
                            </div>
                            @if($order->route_notes)
                                <p class="mt-2 text-xs text-white/70">Notities: {{ $order->route_notes }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <a
                                    href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($order->address . ', ' . $order->postcode . ' ' . $order->city) }}&travelmode=driving"
                                    class="inline-flex items-center justify-center rounded-lg bg-white/10 px-3 py-2 text-xs font-semibold"
                                >
                                    Navigeren
                                </a>
                                @if($order->status !== 'completed')
                                    <form method="POST" action="{{ route('driver.orders.complete', $order) }}">
                                        @csrf
                                        <button class="inline-flex items-center justify-center rounded-lg bg-[color:var(--app-accent)] px-3 py-2 text-xs font-semibold text-black" type="submit">
                                            Afhandelen
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @if(session('toast'))
        <div class="mt-6 rounded-xl border border-[color:var(--app-border)] bg-[color:var(--app-card-2)] px-4 py-3 text-sm text-[color:var(--app-accent)]">
            {{ session('toast') }}
        </div>
    @endif
</div>
@endsection
