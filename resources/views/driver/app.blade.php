@extends('driver.layouts.app')

@section('title', 'Mijn route')

@section('content')
@php
    $completedCount = $orders->filter(fn ($order) => $order->status->value === 'completed')->count();
    $openCount = $orders->count() - $completedCount;
    $progress = $orders->isNotEmpty() ? (int) round(($completedCount / $orders->count()) * 100) : 0;
    $routeDay = \Carbon\Carbon::parse($routeDate);
@endphp

<div class="driver-shell app-fade-in">
    <header class="driver-header">
        <a href="{{ route('driver.app') }}" class="driver-brand" aria-label="Routeoverzicht">
            <span class="driver-brand__mark">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 17.5h16M6.5 17.5V9.8a2 2 0 0 1 1.2-1.83l3.5-1.5a2 2 0 0 1 1.6 0l3.5 1.5a2 2 0 0 1 1.2 1.83v7.7M9 17.5v-4h6v4M9 10h.01M15 10h.01"/>
                </svg>
            </span>
            <span>
                <strong>Route<span>flow</span></strong>
                <small>Kachelvloeistof.nl</small>
            </span>
        </a>

        <div class="driver-profile">
            <div class="driver-profile__copy">
                <span>Chauffeur</span>
                <strong>{{ auth()->user()->name }}</strong>
            </div>
            <div class="driver-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
        </div>
    </header>

    @if(session('toast'))
        <div class="driver-toast" role="status">
            <span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
            </span>
            <div><strong>Route bijgewerkt</strong><small>{{ session('toast') }}</small></div>
        </div>
    @endif

    <section class="driver-hero">
        <div class="driver-hero__copy">
            <p class="driver-eyebrow">
                <span></span>
                {{ $routeDay->isToday() ? 'Vandaag onderweg' : $routeDay->translatedFormat('l') }}
            </p>
            <h1>{{ $routeDay->translatedFormat('d F Y') }}</h1>
            <p>
                @if($orders->isEmpty())
                    Er is voor deze dag nog geen route ingepland.
                @elseif($openCount === 0)
                    Lekker gewerkt — alle stops zijn afgerond.
                @else
                    Nog {{ $openCount }} {{ \Illuminate\Support\Str::plural('stop', $openCount) }} te gaan. Goede reis!
                @endif
            </p>
        </div>

        <form method="GET" action="{{ route('driver.app') }}" class="driver-filter">
            <label>
                <span>Datum</span>
                <input type="date" name="route_date" value="{{ $routeDate }}">
            </label>
            @if($driverRoutes->isNotEmpty())
                <label>
                    <span>Route</span>
                    <select name="route_id">
                        @foreach($driverRoutes as $route)
                            <option value="{{ $route->id }}" @selected($selectedRoute?->id === $route->id)>
                                {{ $route->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endif
            <button type="submit" aria-label="Route tonen">
                Toon route
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </form>
    </section>

    <section class="driver-overview" aria-label="Route voortgang">
        <div class="driver-progress-card">
            <div class="driver-progress-card__top">
                <div>
                    <span>Voortgang</span>
                    <strong>{{ $completedCount }} van {{ $orders->count() }} stops</strong>
                </div>
                <strong class="driver-progress-card__percent">{{ $progress }}%</strong>
            </div>
            <div class="driver-progress" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                <span style="width: {{ $progress }}%"></span>
            </div>
        </div>

        <div class="driver-stat">
            <span class="driver-stat__icon driver-stat__icon--open">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l2.5 1.5"/></svg>
            </span>
            <span><small>Openstaand</small><strong>{{ $openCount }}</strong></span>
        </div>

        <div class="driver-stat">
            <span class="driver-stat__icon driver-stat__icon--done">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
            </span>
            <span><small>Afgerond</small><strong>{{ $completedCount }}</strong></span>
        </div>
    </section>

    <div class="driver-content">
        <section class="driver-stops">
            <div class="driver-section-heading">
                <div>
                    <p class="driver-eyebrow"><span></span>Dagplanning</p>
                    <h2>Jouw stops</h2>
                </div>
                <span class="driver-route-count">{{ $orders->count() }} {{ \Illuminate\Support\Str::plural('adres', $orders->count()) }}</span>
            </div>

            @if($orders->isEmpty())
                <div class="driver-empty">
                    <span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V8l8-4 8 4v11M8 19v-6h8v6"/></svg>
                    </span>
                    <h3>Geen stops gevonden</h3>
                    <p>Kies een andere datum of neem contact op met de routeplanner.</p>
                </div>
            @else
                <div class="driver-stop-list">
                    @foreach($orders as $order)
                        @php
                            $isCompleted = $order->status->value === 'completed';
                            $payment = $order->latestPayment;
                            $isCash = $payment?->isCash() ?? false;
                            $cashPending = $payment?->isCashPending() ?? false;
                        @endphp
                        <article class="driver-stop {{ $isCompleted ? 'driver-stop--completed' : '' }}">
                            <div class="driver-stop__rail">
                                <span class="driver-stop__number">
                                    @if($isCompleted)
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                    @else
                                        {{ $order->route_sequence ?? $loop->iteration }}
                                    @endif
                                </span>
                                @unless($loop->last)<i></i>@endunless
                            </div>

                            <div class="driver-stop__card">
                                <div class="driver-stop__head">
                                    <div>
                                        <span class="driver-stop__label">Stop {{ $order->route_sequence ?? $loop->iteration }} · Order #{{ $order->id }}</span>
                                        <h3>{{ $order->name }}</h3>
                                    </div>
                                    <span class="driver-status {{ $isCompleted ? 'driver-status--done' : '' }}">
                                        <i></i>{{ $isCompleted ? 'Afgerond' : 'Open' }}
                                    </span>
                                </div>

                                <div class="driver-address">
                                    <span>
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                    </span>
                                    <p>{{ $order->address }}<br><strong>{{ $order->postcode }} {{ $order->city }}</strong></p>
                                </div>

                                @if($isCash)
                                    <div class="driver-cash {{ $cashPending ? 'driver-cash--open' : 'driver-cash--paid' }}">
                                        <span class="driver-cash__icon" aria-hidden="true">
                                            @if($cashPending)
                                                <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M7 9h.01M17 15h.01"/></svg>
                                            @else
                                                <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>
                                            @endif
                                        </span>
                                        <div>
                                            <small>Contante betaling</small>
                                            <strong>€ {{ number_format($payment->amount, 2, ',', '.') }}</strong>
                                            <p>{{ $cashPending ? 'Nog te ontvangen van de klant' : 'Ontvangen op '.$payment->paid_at?->format('d-m-Y H:i') }}</p>
                                        </div>
                                        @if($cashPending)
                                            <form method="POST" action="{{ route('driver.orders.cash-received', $order) }}" onsubmit="return confirm('Bevestig dat je € {{ number_format($payment->amount, 2, ',', '.') }} contant hebt ontvangen.')">
                                                @csrf
                                                <button type="submit">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                                    Contant ontvangen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                                @if($order->route_notes)
                                    <div class="driver-note">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v5M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                                        <p><strong>Notitie</strong>{{ $order->route_notes }}</p>
                                    </div>
                                @endif

                                <div class="driver-stop__actions">
                                    <a
                                        href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($order->address . ', ' . $order->postcode . ' ' . $order->city) }}&travelmode=driving"
                                        class="driver-button driver-button--nav"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 11 16-7-7 16-2-7-7-2Z"/></svg>
                                        Navigeer
                                    </a>
                                    @unless($isCompleted)
                                        <form method="POST" action="{{ route('driver.orders.complete', $order) }}">
                                            @csrf
                                            <button class="driver-button driver-button--complete" type="submit">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                                                Markeer afgerond
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="driver-sidebar">
            <div class="driver-route-card">
                <span class="driver-route-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19V8M19 16V5M5 8c0-3 5-3 5 0v8c0 3 5 3 5 0V8c0-3 4-3 4 0"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="5" r="2"/></svg>
                </span>
                <p class="driver-eyebrow"><span></span>Volledige route</p>
                <h2>{{ $selectedRoute?->name ?? 'Nog geen route' }}</h2>
                <p>Open alle stops in één keer in Google Maps en start direct met rijden.</p>
                <a href="{{ $routeMapUrl ?? '#' }}" class="driver-route-button {{ $routeMapUrl ? '' : 'driver-route-button--disabled' }}" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 11 16-7-7 16-2-7-7-2Z"/></svg>
                    Start de route
                </a>
            </div>

            <a href="{{ route('admin.routes.index', ['route_date' => $routeDate, 'route_id' => $selectedRoute?->id]) }}" class="driver-planner-link">
                <span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3v3M17 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z"/></svg>
                </span>
                <span><strong>Routeplanning</strong><small>Bekijk en beheer de planning</small></span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </aside>
    </div>
</div>
@endsection
