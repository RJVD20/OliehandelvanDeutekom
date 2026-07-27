@php
    $initialRule = $productRule ?? null;
    $initialEnabled = (bool) old('tier_pricing_enabled', $initialRule !== null);
    $initialDeliveryTiers = old('delivery_tiers', $initialRule['delivery'] ?? []);
    $initialPickupTiers = old('pickup_tiers', $initialRule['pickup'] ?? []);
@endphp

<div
    class="tier-pricing-editor rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
    x-data="tierPricingEditor(
        @js($initialEnabled),
        @js($initialDeliveryTiers),
        @js($initialPickupTiers)
    )"
>
    <div class="flex items-start gap-3">
        <input type="hidden" name="tier_pricing_enabled" value="0">
        <input
            id="tier-pricing-enabled"
            type="checkbox"
            name="tier_pricing_enabled"
            value="1"
            x-model="tierPricingEnabled"
            class="mt-1 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
        >
        <label for="tier-pricing-enabled" class="cursor-pointer">
            <span class="block text-sm font-bold text-gray-800">Staffelprijzen gebruiken</span>
            <span class="mt-0.5 block text-xs text-gray-500">Geef korting wanneer een klant meerdere stuks van dit product bestelt.</span>
        </label>
    </div>

    <div x-show="tierPricingEnabled" x-cloak class="mt-6 grid gap-6 lg:grid-cols-2">
        @foreach(['delivery' => 'Thuisbezorgen', 'pickup' => 'Afhalen'] as $method => $label)
            <section class="rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">{{ $label }}</h3>
                        <p class="text-xs text-gray-500">Vanaf welk aantal geldt welke stukprijs?</p>
                    </div>
                    <button type="button" @click="addTier('{{ $method }}')" class="shrink-0 text-xs font-semibold text-green-700 hover:underline">
                        + Prijsregel
                    </button>
                </div>

                <div class="mt-3 space-y-2">
                    <template x-for="(tier, index) in {{ $method }}Tiers" :key="index">
                        <div class="relative rounded-lg bg-gray-50 p-3 pr-12">
                            <div class="grid min-w-0 grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-1">
                                <label class="block text-xs font-semibold text-gray-700">
                                    Vanaf aantal
                                    <input type="number" min="1" max="999" required x-model="tier.quantity" :name="`{{ $method }}_tiers[${index}][quantity]`" placeholder="Bijv. 3" class="mt-1.5 block w-full rounded-lg border border-gray-400 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-gray-500 focus:ring-0">
                                </label>
                                <label class="block text-xs font-semibold text-gray-700">
                                    Prijs per stuk
                                    <span class="mt-1.5 flex items-center rounded-lg border border-gray-400 bg-white shadow-sm focus-within:border-gray-500">
                                        <span class="px-2 text-gray-500">€</span>
                                        <input type="number" step="0.01" min="0" required x-model="tier.price" :name="`{{ $method }}_tiers[${index}][price]`" placeholder="0,00" class="min-w-0 flex-1 rounded-r-lg border-0 bg-transparent px-2 py-2.5 text-sm text-gray-900 focus:ring-0">
                                    </span>
                                </label>
                            </div>
                            <button type="button" @click="removeTier('{{ $method }}', index)" class="absolute right-2 top-2 flex h-9 w-9 items-center justify-center rounded text-xl text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="Prijsregel verwijderen">×</button>
                            <p class="mt-2 text-xs font-medium text-green-700" x-show="tier.quantity && tier.price">
                                Vanaf <span x-text="tier.quantity"></span> stuks betaalt de klant € <span x-text="formatPrice(tier.price)"></span> per stuk.
                            </p>
                        </div>
                    </template>

                    <p x-show="{{ $method }}Tiers.length === 0" class="rounded-lg bg-gray-50 p-3 text-xs text-gray-500">
                        Geen prijsregels: de normale productprijs blijft gelden.
                    </p>
                </div>
            </section>
        @endforeach
    </div>
</div>

@once
    <script>
        function tierPricingEditor(enabled, deliveryTiers, pickupTiers) {
            return {
                tierPricingEnabled: Boolean(enabled),
                deliveryTiers: deliveryTiers ?? [],
                pickupTiers: pickupTiers ?? [],
                addTier(method) {
                    this[method + 'Tiers'].push({ quantity: '', price: '' });
                },
                removeTier(method, index) {
                    this[method + 'Tiers'].splice(index, 1);
                },
                formatPrice(value) {
                    return Number(value || 0).toFixed(2).replace('.', ',');
                },
            };
        }
    </script>
@endonce
