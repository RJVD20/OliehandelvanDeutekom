@extends('themes.default.layouts.app')

@section('title', 'Mijn bestellingen')

@section('content')
@php
    $orders = $orders ?? auth()->user()
        ->orders()
        ->placed()
        ->withCount('items')
        ->latest()
        ->get();

    $statusStyles = [
        'pending' => ['In behandeling', 'account-order-status--pending'],
        'shipped' => ['Verzonden', 'account-order-status--shipped'],
        'completed' => ['Afgerond', 'account-order-status--completed'],
        'cancelled' => ['Geannuleerd', 'account-order-status--cancelled'],
    ];
    $totalSpent = $orders->reject(fn ($order) => $order->status->value === 'cancelled')->sum('total');
    $latestOrder = $orders->first();
@endphp

<section class="account-orders-hero">
    <div class="account-orders-hero__content">
        <div>
            <p class="turbo-section-label">Mijn account</p>
            <h1>Mijn bestellingen</h1>
            <p>Bekijk de status, details en facturen van je eerdere bestellingen.</p>
        </div>
        <a href="{{ route('products.index') }}" class="account-orders-shop-link">
            Naar de webshop
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14m-5-5 5 5-5 5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>

    @if($orders->isNotEmpty())
        <div class="account-orders-stats" aria-label="Samenvatting van je bestellingen">
            <div>
                <span class="account-orders-stats__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 8h12l-1 12H7L6 8Zm3 0V6a3 3 0 0 1 6 0v2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>
                    <small>Bestellingen</small>
                    <strong>{{ $orders->count() }}</strong>
                </span>
            </div>
            <div>
                <span class="account-orders-stats__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 7h16v12H4zM4 10h16M8 15h3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>
                    <small>Totaal besteed</small>
                    <strong>€ {{ number_format($totalSpent, 2, ',', '.') }}</strong>
                </span>
            </div>
            <div>
                <span class="account-orders-stats__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="8" stroke-width="1.8"/><path d="M12 8v4l3 2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>
                    <small>Laatste bestelling</small>
                    <strong>{{ $latestOrder->created_at->translatedFormat('j M Y') }}</strong>
                </span>
            </div>
        </div>
    @endif
</section>

@if($orders->isNotEmpty())
    <section class="account-orders-list" aria-labelledby="orders-list-title">
        <div class="account-orders-list__heading">
            <div>
                <p class="turbo-section-label">Bestelhistorie</p>
                <h2 id="orders-list-title">Alle bestellingen</h2>
            </div>
            <span>{{ $orders->count() }} {{ $orders->count() === 1 ? 'bestelling' : 'bestellingen' }}</span>
        </div>

        <div class="account-orders-grid">
            @foreach($orders as $order)
                @php
                    [$statusLabel, $statusClass] = $statusStyles[$order->status->value]
                        ?? [ucfirst(str_replace('_', ' ', $order->status->value)), 'account-order-status--pending'];
                @endphp
                <article class="account-order-card">
                    <div class="account-order-card__top">
                        <div class="account-order-card__number">
                            <span aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 3h10v18l-2.5-1.7L12 21l-2.5-1.7L7 21V3Z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 8h4m-4 4h4" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <div>
                                <small>Bestelling</small>
                                <h3>#{{ $order->id }}</h3>
                            </div>
                        </div>
                        <span class="account-order-status {{ $statusClass }}">
                            <i aria-hidden="true"></i>{{ $statusLabel }}
                        </span>
                    </div>

                    <dl class="account-order-card__details">
                        <div>
                            <dt>Besteld op</dt>
                            <dd>{{ $order->created_at->translatedFormat('j F Y') }}</dd>
                        </div>
                        <div>
                            <dt>Productregels</dt>
                            <dd>{{ $order->items_count }} {{ $order->items_count === 1 ? 'product' : 'producten' }}</dd>
                        </div>
                        <div>
                            <dt>Totaalbedrag</dt>
                            <dd>€ {{ number_format($order->total, 2, ',', '.') }}</dd>
                        </div>
                    </dl>

                    <a href="{{ route('account.orders.show', $order) }}" class="account-order-card__link" aria-label="Bekijk bestelling nummer {{ $order->id }}">
                        Bekijk bestelling
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14m-5-5 5 5-5 5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@else
    <section class="account-orders-empty">
        <span class="account-orders-empty__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 8h12l-1 12H7L6 8Zm3 0V6a3 3 0 0 1 6 0v2" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <p class="turbo-section-label">Nog lekker overzichtelijk</p>
        <h2>Je hebt nog geen bestellingen geplaatst</h2>
        <p>Zodra je iets bestelt, vind je hier de status, details en factuur terug.</p>
        <a href="{{ route('products.index') }}" class="turbo-button px-6 py-3 text-sm">Bekijk het assortiment</a>
    </section>
@endif

@endsection
