@extends('themes.default.layouts.app')

@section('title', 'Bestelling #' . $order->id)

@section('content')
@php
    $statusStyles = [
        'pending' => ['In behandeling', 'account-order-status--pending', 'We hebben je bestelling ontvangen en gaan ermee aan de slag.'],
        'shipped' => ['Verzonden', 'account-order-status--shipped', 'Je bestelling is onderweg naar het opgegeven adres.'],
        'completed' => ['Afgerond', 'account-order-status--completed', 'Deze bestelling is volledig afgerond.'],
        'cancelled' => ['Geannuleerd', 'account-order-status--cancelled', 'Deze bestelling is geannuleerd.'],
    ];
    [$statusLabel, $statusClass, $statusDescription] = $statusStyles[$order->status->value]
        ?? [ucfirst(str_replace('_', ' ', $order->status->value)), 'account-order-status--pending', 'Bekijk hieronder de gegevens van je bestelling.'];
    $itemsSubtotal = $order->items->sum(fn ($item) => $item->price * $item->quantity);
    $shippingCost = (float) ($order->shipping_cost ?? max(0, (float) $order->total - $itemsSubtotal));
    $isPickup = $order->fulfillment_method === 'pickup';
@endphp

<nav class="account-order-breadcrumb" aria-label="Broodkruimel">
    <a href="{{ route('account.orders') }}">
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m15 18-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Terug naar mijn bestellingen
    </a>
</nav>

<section class="account-order-detail-hero">
    <div class="account-order-detail-hero__main">
        <div class="account-order-detail-hero__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 3h10v18l-2.5-1.7L12 21l-2.5-1.7L7 21V3Z" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 8h4m-4 4h4" stroke-width="1.7" stroke-linecap="round"/></svg>
        </div>
        <div>
            <p class="turbo-section-label">Bestelgegevens</p>
            <h1>Bestelling #{{ $order->id }}</h1>
            <p>Geplaatst op {{ $order->created_at->translatedFormat('j F Y \o\m H:i') }}</p>
        </div>
    </div>
    <div class="account-order-detail-hero__status">
        <span class="account-order-status {{ $statusClass }}"><i aria-hidden="true"></i>{{ $statusLabel }}</span>
        <p>{{ $statusDescription }}</p>
    </div>
</section>

<div class="account-order-detail-grid">
    <section class="account-order-detail-products" aria-labelledby="products-title">
        <div class="account-order-detail-section-heading">
            <div>
                <p class="turbo-section-label">Inhoud</p>
                <h2 id="products-title">Producten</h2>
            </div>
            <span>{{ $order->items->sum('quantity') }} {{ $order->items->sum('quantity') === 1 ? 'artikel' : 'artikelen' }}</span>
        </div>

        <div class="account-order-product-list">
            @foreach($order->items as $item)
                <div class="account-order-product">
                    <div class="account-order-product__image">
                        @if($item->product?->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" loading="lazy">
                        @else
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 8h12l-1 12H7L6 8Zm3 0V6a3 3 0 0 1 6 0v2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @endif
                    </div>
                    <div class="account-order-product__name">
                        <strong>{{ $item->product_name }}</strong>
                        <small>{{ $item->quantity }} × € {{ number_format($item->price, 2, ',', '.') }}</small>
                    </div>
                    <strong class="account-order-product__price">€ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>

        <dl class="account-order-totals">
            <div>
                <dt>Subtotaal</dt>
                <dd>€ {{ number_format($itemsSubtotal, 2, ',', '.') }}</dd>
            </div>
            @if($shippingCost > 0)
                <div>
                    <dt>Bezorgkosten</dt>
                    <dd>€ {{ number_format($shippingCost, 2, ',', '.') }}</dd>
                </div>
            @endif
            <div class="account-order-totals__total">
                <dt>Totaal</dt>
                <dd>€ {{ number_format($order->total, 2, ',', '.') }}</dd>
            </div>
        </dl>

        <div class="account-order-actions">
            <form action="{{ route('account.orders.reorder', $order) }}" method="POST">
                @csrf
                <button type="submit" class="turbo-button account-order-actions__primary">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 8v5h-5M4 16v-5h5m9.5 1A7 7 0 0 0 6.6 7.1L4 11m16 2-2.6 3.9A7 7 0 0 1 5.5 12" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Opnieuw in winkelmand
                </button>
            </form>
            <a href="{{ route('account.orders.invoice', $order) }}" class="account-order-actions__secondary" target="_blank" rel="noopener">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 3h7l4 4v14H7V3Zm7 0v5h4M10 13h5m-5 4h5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Factuur downloaden
            </a>
        </div>
    </section>

    <aside class="account-order-detail-sidebar">
        <section class="account-order-info-card">
            <div class="account-order-detail-section-heading">
                <div>
                    <p class="turbo-section-label">{{ $isPickup ? 'Afhalen' : 'Aflevering' }}</p>
                    <h2>{{ $isPickup ? 'Afhaallocatie' : 'Bezorggegevens' }}</h2>
                </div>
            </div>
            <div class="account-order-address">
                <span aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" stroke-width="1.7"/><circle cx="12" cy="10" r="2.3" stroke-width="1.7"/></svg>
                </span>
                <address>
                    @if($isPickup)
                        <strong>{{ $order->pickup_location_name ?: 'Gekozen afhaallocatie' }}</strong>
                        {{ $order->pickup_location_address ?: 'Adres niet beschikbaar' }}
                        @if($order->pickup_location_opening)
                            <br>{{ $order->pickup_location_opening }}
                        @endif
                    @else
                        <strong>{{ $order->name }}</strong>
                        {{ $order->address }}<br>
                        {{ $order->postcode }} {{ $order->city }}
                    @endif
                </address>
            </div>
        </section>

        <section class="account-order-info-card">
            <div class="account-order-detail-section-heading">
                <div>
                    <p class="turbo-section-label">Contact</p>
                    <h2>Contactgegevens</h2>
                </div>
            </div>
            <dl class="account-order-contact-list">
                <div>
                    <dt>E-mailadres</dt>
                    <dd>{{ $order->email }}</dd>
                </div>
                @if($order->phone)
                    <div>
                        <dt>Telefoonnummer</dt>
                        <dd>{{ $order->phone }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <div class="account-order-help">
            <span aria-hidden="true">?</span>
            <div>
                <strong>Vraag over je bestelling?</strong>
                <p>Neem gerust contact met ons op en vermeld bestelnummer #{{ $order->id }}.</p>
            </div>
        </div>
    </aside>
</div>

@endsection
