@php
    $normalValue = $promotion->normalValue();
    $hasSaving = $normalValue > (float) $promotion->fixed_price;
@endphp

<article class="promotion-card">
    <a href="{{ route('product.show', $promotion->mainProduct->slug) }}" class="promotion-card__media">
        @if($promotion->imageUrl())
            <img src="{{ $promotion->imageUrl() }}" alt="{{ $promotion->image_alt ?: $promotion->title }}" loading="lazy">
        @endif
        <span>Actie</span>
    </a>
    <div class="promotion-card__body">
        <div>
            <h3>{{ $promotion->title }}</h3>
            <p>{{ $promotion->short_description }}</p>
        </div>
        <div class="promotion-card__footer">
            <div>
                @if($hasSaving)
                    <small>Normaal € {{ number_format($normalValue, 2, ',', '.') }}</small>
                @else
                    <small>Inclusief gratis verzending</small>
                @endif
                <strong>€ {{ number_format($promotion->fixed_price, 2, ',', '.') }}</strong>
            </div>
            <form method="POST" action="{{ route('cart.add-promotion', $promotion) }}">
                @csrf
                <button type="submit" class="turbo-button">Kies actie</button>
            </form>
        </div>
    </div>
</article>
