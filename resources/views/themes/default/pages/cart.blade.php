@extends('themes.default.layouts.app')

@section('title', 'Winkelmand')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <p class="turbo-section-label mb-2">Jouw bestelling</p>
        <h1 class="text-3xl font-bold sm:text-4xl">Winkelmand</h1>
    </div>
    @if(count($cart) > 0)
        <span class="rounded-full bg-turbo-navy px-3 py-1.5 text-xs font-bold text-white">
            {{ collect($cart)->sum('quantity') }} {{ collect($cart)->sum('quantity') === 1 ? 'artikel' : 'artikelen' }}
        </span>
    @endif
</div>

@if(count($cart) > 0)
    @php
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    @endphp

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-5">
            <form method="POST" action="{{ route('cart.fulfillment') }}" class="turbo-card overflow-hidden">
                @csrf
                <fieldset>
                    <div class="border-b border-gray-100 px-5 py-4">
                        <legend class="font-bold text-turbo-ink">Ontvangst van je bestelling</legend>
                        <p class="mt-1 text-sm text-gray-500">De bijbehorende staffelprijzen worden direct toegepast.</p>
                    </div>
                    <div class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5">
                        @foreach([
                            'delivery' => ['Thuisbezorgen', 'Gratis volgens de bezorgstaffel', 'truck'],
                            'pickup' => ['Afhalen', 'Bij een van onze depots', 'pin'],
                        ] as $method => [$label, $description, $icon])
                            <label class="cursor-pointer">
                                <input type="radio" name="fulfillment_method" value="{{ $method }}" class="peer sr-only" @checked($fulfillmentMethod === $method) onchange="this.form.submit()">
                                <span class="flex h-full items-center gap-3 rounded-xl border border-gray-200 p-4 transition peer-checked:border-turbo-gold peer-checked:bg-turbo-gold/10 peer-checked:ring-2 peer-checked:ring-turbo-gold/20 hover:border-turbo-gold/60">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-turbo-navy text-turbo-gold">
                                        @if($icon === 'truck')
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></svg>
                                        @else
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
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

            @if($fulfillmentMethod === 'delivery')
                @include('themes.default.components.delivery-notice', [
                    'detailed' => true,
                    'attributes' => new \Illuminate\View\ComponentAttributeBag(),
                ])
            @endif

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
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                            <span>€ {{ number_format($item['price'], 2, ',', '.') }} per stuk</span>
                                            @if($item['tier_applied'])
                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 font-bold text-emerald-700">Staffelprijs</span>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('cart.remove', $id) }}" class="sm:hidden">
                                        @csrf
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="Verwijder {{ $item['name'] }}">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-4 flex items-center justify-between sm:justify-start sm:gap-5">
                                    <div x-data="{ qty: {{ $item['quantity'] }}, busy: false }" class="inline-flex items-center overflow-hidden rounded-xl border border-gray-200 bg-white">
                                        <button type="button" @click="if (qty > 1 && !busy) { busy = true; qty--; $nextTick(() => $refs.form.submit()) }" :disabled="busy || qty <= 1" class="inline-flex h-10 w-10 items-center justify-center text-lg font-bold text-turbo-ink hover:bg-gray-50 disabled:opacity-40" aria-label="Aantal verlagen">−</button>
                                        <form x-ref="form" method="POST" action="{{ route('cart.update', $id) }}">
                                            @csrf
                                            <input type="hidden" name="quantity" x-model="qty">
                                            <span x-text="qty" class="inline-flex h-10 min-w-10 items-center justify-center border-x border-gray-200 px-2 font-bold text-turbo-ink"></span>
                                        </form>
                                        <button type="button" @click="if (!busy) { busy = true; qty++; $nextTick(() => $refs.form.submit()) }" :disabled="busy" class="inline-flex h-10 w-10 items-center justify-center text-lg font-bold text-turbo-ink hover:bg-gray-50 disabled:opacity-40" aria-label="Aantal verhogen">+</button>
                                    </div>
                                    <strong class="product-card__price sm:hidden">€ {{ number_format($subtotal, 2, ',', '.') }}</strong>
                                </div>
                            </div>

                            <div class="hidden min-w-[8rem] text-right sm:block">
                                <strong class="product-card__price text-lg">€ {{ number_format($subtotal, 2, ',', '.') }}</strong>
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
                    <p class="mt-0.5 text-xs text-white/65">{{ collect($cart)->sum('quantity') }} artikelen</p>
                </div>
                <div class="space-y-4 p-5">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4 text-gray-600">
                            <span>Producten</span>
                            <span>€ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between gap-4 text-gray-600">
                            <span>{{ $fulfillmentMethod === 'delivery' ? 'Bezorging' : 'Afhalen' }}</span>
                            <span class="font-semibold text-emerald-700">Gratis</span>
                        </div>
                    </div>
                    <div class="flex items-end justify-between gap-4 border-t border-gray-100 pt-4">
                        <span class="font-bold text-turbo-ink">Totaal</span>
                        <span class="product-card__price text-2xl">€ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                    <p class="text-xs leading-5 text-gray-500">
                        {{ $fulfillmentMethod === 'delivery' ? 'Inclusief gratis thuisbezorging volgens de gekozen staffel.' : 'De afhaalstaffel is toegepast.' }}
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
