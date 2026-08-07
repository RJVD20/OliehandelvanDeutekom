@extends('themes.default.layouts.app')

@section('title', 'Winkelmand')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <p class="turbo-section-label mb-2">Jouw bestelling</p>
        <h1 class="text-3xl font-bold sm:text-4xl">Winkelmand</h1>
    </div>
    @if(count($cart) > 0)
        <span data-cart-heading-count class="rounded-full bg-turbo-navy px-3 py-1.5 text-xs font-bold text-white">
            {{ collect($cart)->sum('quantity') }} {{ collect($cart)->sum('quantity') === 1 ? 'artikel' : 'artikelen' }}
        </span>
    @endif
</div>

@if(count($cart) > 0)
    @php
        $productTotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $total = $productTotal + $deliveryCosts['total'];
        $baseTotal = collect($cart)->sum(fn ($item) => $item['base_price'] * $item['quantity']);
        $totalDiscount = collect($cart)->sum('discount_total');
    @endphp

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-5">
            <form method="POST" action="{{ route('cart.delivery-choice') }}" class="turbo-card overflow-hidden">
                @csrf
                <fieldset>
                    <div class="border-b border-gray-100 px-5 py-4">
                        <legend class="font-bold text-turbo-ink">Hoe wil je je bestelling ontvangen?</legend>
                        <p class="mt-1 text-sm text-gray-500">Kies bezorgen of gratis afhalen bij een depot.</p>
                    </div>
                    <div class="grid gap-3 p-4 md:grid-cols-3 sm:p-5">
                        @php
                            $selectedDeliveryOption = $fulfillmentMethod === 'pickup'
                                ? 'pickup'
                                : $deliveryService;
                            $standardDescription = $deliveryCosts['jerrycans'] >= 3
                                ? 'Gratis · levering binnen 4–8 werkdagen'
                                : '€ 5,00 · levering binnen 4–8 werkdagen';
                            $expressDescription = '€ 10,00 per bestelling · volgende dag bij bestelling vóór 12.00 uur';
                        @endphp
                        @foreach([
                            'standard' => ['Standaard bezorgen', $standardDescription, 'truck'],
                            'express' => ['Express Premium', $expressDescription, 'bolt'],
                            'pickup' => ['Afhalen', 'Gratis bij een van onze depots', 'pin'],
                        ] as $option => [$label, $description, $icon])
                            <label class="cursor-pointer">
                                <input type="radio" name="delivery_option" value="{{ $option }}" class="peer sr-only" @checked($selectedDeliveryOption === $option) onchange="this.form.submit()">
                                <span class="flex h-full items-start gap-3 rounded-xl border border-gray-200 p-4 transition peer-checked:border-turbo-gold peer-checked:bg-turbo-gold/10 peer-checked:ring-2 peer-checked:ring-turbo-gold/20 hover:border-turbo-gold/60">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-turbo-navy text-turbo-gold">
                                        @if($icon === 'truck')
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>
                                        @elseif($icon === 'pin')
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                        @else
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m13 2-8 12h7l-1 8 8-12h-7z"/></svg>
                                        @endif
                                    </span>
                                    <span>
                                        <strong class="block text-turbo-ink">{{ $label }}</strong>
                                        <span class="mt-0.5 block text-xs text-gray-500">{{ $description }}</span>
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </form>

            <section class="space-y-3" aria-label="Producten in winkelmand">
                @foreach($cart as $id => $item)
                    @php $subtotal = $item['price'] * $item['quantity']; @endphp
                    <article class="turbo-card overflow-hidden p-3 sm:p-4">
                        <div class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-4 sm:grid-cols-[7rem_minmax(0,1fr)_auto] sm:items-center">
                            <a href="{{ route('product.show', $item['slug']) }}" class="flex h-24 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-white sm:h-28" aria-label="Bekijk {{ $item['name'] }}">
                                @if($item['image'])
                                    <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-contain p-2" loading="lazy">
                                @else
                                    <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-turbo-navy to-turbo-blue px-2 text-center text-xs font-bold text-white">
                                        {{ $item['name'] }}
                                    </span>
                                @endif
                            </a>

                            <div class="min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <a href="{{ route('product.show', $item['slug']) }}" class="font-bold leading-snug text-turbo-ink hover:text-turbo-gold">{{ $item['name'] }}</a>
                                        @if(!empty($item['promotion_id']))
                                            <div class="mt-2 rounded-lg border border-turbo-gold/40 bg-turbo-gold/10 p-2.5 text-xs">
                                                <strong class="block text-turbo-navy">{{ $item['promotion_title'] }}</strong>
                                                <ul class="mt-1 space-y-0.5 text-gray-600">@foreach($item['promotion_items'] as $bundleItem)<li>+ {{ $bundleItem['quantity'] }}× {{ $bundleItem['label'] ?: $bundleItem['name'] }} @if($bundleItem['role']==='free')<strong class="text-emerald-700">gratis</strong>@endif</li>@endforeach @if($item['free_shipping'])<li class="font-bold text-emerald-700">+ Gratis standaardverzending</li>@endif</ul>
                                            </div>
                                        @endif
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                            <span data-cart-unit-price="{{ $id }}">€ {{ number_format($item['price'], 2, ',', '.') }} per stuk</span>
                                            <span data-cart-base-price="{{ $id }}" @class([
                                                'line-through',
                                                'hidden' => $item['discount_total'] <= 0,
                                            ])>€ {{ number_format($item['base_price'], 2, ',', '.') }}</span>
                                            <span data-cart-tier="{{ $id }}" @class([
                                                'rounded-full bg-emerald-100 px-2 py-0.5 font-bold text-emerald-700',
                                                'hidden' => $item['discount_total'] <= 0,
                                            ])>{{ !empty($item['promotion_id']) ? 'Actievoordeel' : 'Staffelkorting' }}</span>
                                        </div>
                                        <p data-cart-item-discount="{{ $id }}" @class([
                                            'mt-1.5 text-xs font-semibold text-emerald-700',
                                            'hidden' => $item['discount_total'] <= 0,
                                        ])>Je bespaart € {{ number_format($item['discount_total'], 2, ',', '.') }}</p>

                                        @if($item['tier_progress'])
                                            @php
                                                $currentTier = $item['tier_progress']['current'];
                                                $nextTier = $item['tier_progress']['next'];
                                            @endphp
                                            <div data-cart-tier-card="{{ $id }}" class="mt-3 max-w-md rounded-xl border border-turbo-gold/40 bg-turbo-navy/[0.04] p-3 text-xs">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p data-cart-current-tier="{{ $id }}" class="font-semibold text-turbo-navy">
                                                        @if($currentTier)
                                                            Vanaf {{ $currentTier['quantity'] }} stuks: € {{ number_format($currentTier['price'], 2, ',', '.') }} per stuk
                                                        @else
                                                            Basisprijs: € {{ number_format($item['base_price'], 2, ',', '.') }} per stuk
                                                        @endif
                                                    </p>
                                                    <span class="rounded-full bg-turbo-navy px-2 py-0.5 font-bold text-turbo-gold">Actief</span>
                                                </div>
                                                <p data-cart-next-tier="{{ $id }}" class="mt-1 text-turbo-blue">
                                                    @if($nextTier)
                                                        Nog {{ $item['tier_progress']['quantity_needed'] }} {{ $item['tier_progress']['quantity_needed'] === 1 ? 'stuk' : 'stuks' }} voor € {{ number_format($item['tier_progress']['extra_discount_total'], 2, ',', '.') }} extra voordeel.
                                                    @else
                                                        Beste staffel bereikt.
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('cart.remove', $id) }}" class="sm:hidden">
                                        @csrf
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="Verwijder {{ $item['name'] }}">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-4 flex items-center justify-between sm:justify-start sm:gap-5">
                                    <div
                                        x-data="{
                                            qty: {{ $item['quantity'] }},
                                            busy: false,
                                            formatPrice(value) {
                                                const amount = Number(value);
                                                return new Intl.NumberFormat('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                                                    .format(Number.isFinite(amount) ? amount : 0);
                                            },
                                            async updateQuantity(nextQty) {
                                                if (this.busy || nextQty < 1) return;

                                                const previousQty = this.qty;
                                                this.qty = nextQty;
                                                this.busy = true;

                                                try {
                                                    const formData = new FormData(this.$refs.form);
                                                    formData.set('quantity', nextQty);
                                                    const response = await fetch(this.$refs.form.action, {
                                                        method: 'POST',
                                                        headers: {
                                                            'Accept': 'application/json',
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                        },
                                                        body: formData,
                                                    });

                                                    if (!response.ok) throw new Error('Winkelmand bijwerken mislukt');

                                                    const data = await response.json();
                                                    const productId = '{{ (int) $id }}';
                                                    const discountTotal = Number(data.discount_total ?? 0);
                                                    const cartDiscountTotal = Number(data.cart_discount_total ?? 0);
                                                    const baseTotal = Number(data.base_total ?? data.total ?? 0);
                                                    this.qty = data.quantity;

                                                    document.querySelectorAll(`[data-cart-subtotal='${productId}']`).forEach((element) => {
                                                        element.textContent = `€ ${this.formatPrice(data.item_subtotal)}`;
                                                    });
                                                    document.querySelectorAll('[data-cart-total]').forEach((element) => {
                                                        element.textContent = `€ ${this.formatPrice(data.total)}`;
                                                    });
                                                    const deliveryCosts = data.delivery_costs ?? {};
                                                    document.querySelectorAll('[data-cart-shipping]').forEach((element) => {
                                                        const costs = Number(deliveryCosts.total ?? 0);
                                                        element.textContent = costs > 0 ? `€ ${this.formatPrice(costs)}` : 'Gratis';
                                                    });
                                                    document.querySelectorAll('[data-cart-base-total]').forEach((element) => {
                                                        element.textContent = `€ ${this.formatPrice(baseTotal)}`;
                                                    });

                                                    const unitPrice = document.querySelector(`[data-cart-unit-price='${productId}']`);
                                                    if (unitPrice) unitPrice.textContent = `€ ${this.formatPrice(data.unit_price)} per stuk`;

                                                    const tierLabel = document.querySelector(`[data-cart-tier='${productId}']`);
                                                    if (tierLabel) tierLabel.classList.toggle('hidden', discountTotal <= 0);

                                                    const basePrice = document.querySelector(`[data-cart-base-price='${productId}']`);
                                                    if (basePrice) {
                                                        if (data.base_price !== undefined) {
                                                            basePrice.textContent = `€ ${this.formatPrice(data.base_price)}`;
                                                        }
                                                        basePrice.classList.toggle('hidden', discountTotal <= 0);
                                                    }

                                                    const itemDiscount = document.querySelector(`[data-cart-item-discount='${productId}']`);
                                                    if (itemDiscount) {
                                                        itemDiscount.textContent = `Je bespaart € ${this.formatPrice(discountTotal)}`;
                                                        itemDiscount.classList.toggle('hidden', discountTotal <= 0);
                                                    }

                                                    const progress = data.tier_progress;
                                                    const currentTier = document.querySelector(`[data-cart-current-tier='${productId}']`);
                                                    const nextTier = document.querySelector(`[data-cart-next-tier='${productId}']`);
                                                    if (progress && currentTier && nextTier) {
                                                        currentTier.textContent = progress.current
                                                            ? `Vanaf ${progress.current.quantity} stuks: € ${this.formatPrice(progress.current.price)} per stuk`
                                                            : `Basisprijs: € ${this.formatPrice(data.base_price)} per stuk`;

                                                        if (progress.next) {
                                                            const unit = progress.quantity_needed === 1 ? 'stuk' : 'stuks';
                                                            nextTier.textContent = `Nog ${progress.quantity_needed} ${unit} voor € ${this.formatPrice(progress.extra_discount_total)} extra voordeel.`;
                                                        } else {
                                                            nextTier.textContent = 'Beste staffel bereikt.';
                                                        }
                                                    }

                                                    document.querySelectorAll('[data-cart-discount]').forEach((element) => {
                                                        element.textContent = `− € ${this.formatPrice(cartDiscountTotal)}`;
                                                    });
                                                    document.querySelectorAll('[data-cart-discount-row]').forEach((element) => {
                                                        element.classList.toggle('hidden', cartDiscountTotal <= 0);
                                                    });

                                                    const headingCount = document.querySelector('[data-cart-heading-count]');
                                                    if (headingCount) headingCount.textContent = `${data.count} ${data.count === 1 ? 'artikel' : 'artikelen'}`;

                                                    const summaryCount = document.querySelector('[data-cart-summary-count]');
                                                    if (summaryCount) summaryCount.textContent = `${data.count} ${data.count === 1 ? 'artikel' : 'artikelen'}`;

                                                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: data.count }));
                                                    window.location.reload();
                                                } catch (error) {
                                                    this.qty = previousQty;
                                                } finally {
                                                    this.busy = false;
                                                }
                                            }
                                        }"
                                        class="inline-flex items-center overflow-hidden rounded-xl border border-gray-200 bg-white"
                                    >
                                        <button type="button" @click="updateQuantity(qty - 1)" :disabled="busy || qty <= 1" class="inline-flex h-10 w-10 items-center justify-center text-lg font-bold text-turbo-ink hover:bg-gray-50 disabled:opacity-40" aria-label="Aantal verlagen">−</button>
                                        <form x-ref="form" method="POST" action="{{ route('cart.update', $id) }}">
                                            @csrf
                                            <input type="hidden" name="quantity" x-model="qty">
                                            <span x-text="qty" class="inline-flex h-10 min-w-10 items-center justify-center border-x border-gray-200 px-2 font-bold text-turbo-ink"></span>
                                        </form>
                                        <button type="button" @click="updateQuantity(qty + 1)" :disabled="busy" class="inline-flex h-10 w-10 items-center justify-center text-lg font-bold text-turbo-ink hover:bg-gray-50 disabled:opacity-40" aria-label="Aantal verhogen">+</button>
                                    </div>
                                    <strong data-cart-subtotal="{{ $id }}" class="product-card__price sm:hidden">€ {{ number_format($subtotal, 2, ',', '.') }}</strong>
                                </div>
                            </div>

                            <div class="hidden min-w-[8rem] text-right sm:block">
                                <strong data-cart-subtotal="{{ $id }}" class="product-card__price text-lg">€ {{ number_format($subtotal, 2, ',', '.') }}</strong>
                                <p class="mt-1 text-xs text-gray-400">Subtotaal</p>
                                <form method="POST" action="{{ route('cart.remove', $id) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Verwijderen</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        </div>

        <aside class="h-fit xl:sticky xl:top-32">
            <div class="overflow-hidden rounded-2xl border border-turbo-blue/10 bg-white shadow-lg">
                <div class="bg-turbo-navy px-5 py-4 text-white">
                    <h2 class="text-lg font-bold">Overzicht</h2>
                    <p data-cart-summary-count class="mt-0.5 text-xs text-white/65">{{ collect($cart)->sum('quantity') }} {{ collect($cart)->sum('quantity') === 1 ? 'artikel' : 'artikelen' }}</p>
                </div>
                <div class="space-y-4 p-5">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4 text-gray-600">
                            <span>Producten</span>
                            <span data-cart-base-total>€ {{ number_format($baseTotal, 2, ',', '.') }}</span>
                        </div>
                        <div data-cart-discount-row @class([
                            'flex justify-between gap-4 font-semibold text-emerald-700',
                            'hidden' => $totalDiscount <= 0,
                        ])>
                            <span>Prijsvoordeel</span>
                            <span data-cart-discount>− € {{ number_format($totalDiscount, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between gap-4 text-gray-600">
                            <span>{{ $fulfillmentMethod === 'delivery' ? 'Bezorging' : 'Afhalen' }}</span>
                            <span data-cart-shipping class="font-semibold {{ $deliveryCosts['total'] > 0 ? 'text-turbo-ink' : 'text-emerald-700' }}">
                                {{ $deliveryCosts['total'] > 0 ? '€ '.number_format($deliveryCosts['total'], 2, ',', '.') : 'Gratis' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-end justify-between gap-4 border-t border-gray-100 pt-4">
                        <span class="font-bold text-turbo-ink">Totaal</span>
                        <span data-cart-total class="product-card__price text-2xl">€ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <p class="text-xs leading-5 text-gray-500">
                        @if($fulfillmentMethod === 'delivery' && $deliveryService === 'express')
                            Inclusief Express Premium à € 10,00 per bestelling.
                        @elseif($fulfillmentMethod === 'delivery' && $deliveryCosts['standard'] > 0)
                            Inclusief € 5,00 bezorgvergoeding voor bestellingen onder 3 jerrycans.
                        @elseif($fulfillmentMethod === 'delivery')
                            Inclusief gratis thuisbezorging vanaf 3 jerrycans.
                        @else
                            De afhaalstaffel is toegepast.
                        @endif
                    </p>
                    <a href="{{ route('checkout.index') }}" class="turbo-button flex w-full items-center justify-center gap-2 px-5 py-3.5 text-base">
                        Veilig afrekenen
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="block text-center text-sm font-semibold text-turbo-blue hover:text-turbo-gold">Verder winkelen</a>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-center gap-2 text-xs text-gray-500">
                <svg viewBox="0 0 24 24" class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                Veilig betalen via je eigen bank
            </div>
        </aside>
    </div>
@else
    <div class="turbo-card px-6 py-14 text-center">
        <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-turbo-gray text-turbo-navy">
            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4h2l1.5 12h11L20 7H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
        </span>
        <h2 class="mt-4 text-xl font-bold text-turbo-ink">Je winkelmand is nog leeg</h2>
        <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">Bekijk ons assortiment kachels, vloeistoffen en accessoires.</p>
        <a href="{{ route('products.index') }}" class="turbo-button mt-6 inline-flex px-6 py-3">Bekijk producten</a>
    </div>
@endif
@endsection
