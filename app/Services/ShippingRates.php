<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;

class ShippingRates
{
    public function defaults(): array
    {
        return [
            'shipping_rates_title' => 'Nieuwe tarieven 2026',
            'shipping_rates_greeting' => 'Beste klant,',
            'shipping_rates_intro' => 'Hieronder vindt u onze nieuwe tarieven. Wij zijn nog steeds voordeliger dan Haveka en bezorgen uw bestelling uiteraard gratis bij u thuis.',
            'shipping_rates_gtl_name' => 'Witte blanke GTL Turboheating – 20 liter',
            'shipping_rates_gtl_delivery_quantity_3' => '3',
            'shipping_rates_gtl_delivery_3' => '62.50',
            'shipping_rates_gtl_delivery_quantity_5' => '5',
            'shipping_rates_gtl_delivery_5' => '60.00',
            'shipping_rates_gtl_delivery_quantity_10' => '10',
            'shipping_rates_gtl_delivery_10' => '57.50',
            'shipping_rates_gtl_pickup_quantity_3' => '3',
            'shipping_rates_gtl_pickup_3' => '57.50',
            'shipping_rates_gtl_pickup_quantity_5' => '5',
            'shipping_rates_gtl_pickup_5' => '55.00',
            'shipping_rates_gtl_pickup_quantity_10' => '10',
            'shipping_rates_gtl_pickup_10' => '53.00',
            'shipping_rates_c_name' => 'Rode Zuivere C – 20 liter',
            'shipping_rates_c_delivery_quantity_5' => '5',
            'shipping_rates_c_delivery_5' => '45.00',
            'shipping_rates_c_delivery_quantity_10' => '10',
            'shipping_rates_c_delivery_10' => '42.50',
            'shipping_rates_c_pickup_quantity_3' => '3',
            'shipping_rates_c_pickup_3' => '43.00',
            'shipping_rates_c_pickup_quantity_5' => '5',
            'shipping_rates_c_pickup_5' => '40.00',
        ];
    }

    public function values(): array
    {
        $values = collect($this->defaults())
            ->mapWithKeys(function (string $default, string $key) {
                $stored = Setting::get($key, '');

                return [$key => $stored !== '' ? $stored : $default];
            })
            ->all();

        $selectedProducts = Product::query()
            ->whereIn('id', array_filter([
                (int) Setting::get('shipping_rates_gtl_product_id', 0),
                (int) Setting::get('shipping_rates_c_product_id', 0),
            ]))
            ->get()
            ->keyBy('id');

        foreach (['gtl' => 'gtl', 'c' => 'zuivere_c'] as $key => $rateGroup) {
            $productId = (int) Setting::get("shipping_rates_{$key}_product_id", 0);
            $product = $selectedProducts->get($productId)
                ?? Product::query()->where('rate_group', $rateGroup)->orderBy('name')->first();

            if ($product) {
                $values["shipping_rates_{$key}_name"] = $product->name;
            }
        }

        return $values;
    }

    public function selectedProductIds(): array
    {
        return [
            'gtl' => (int) (Setting::get('shipping_rates_gtl_product_id', 0)
                ?: Product::query()->where('rate_group', 'gtl')->value('id')),
            'c' => (int) (Setting::get('shipping_rates_c_product_id', 0)
                ?: Product::query()->where('rate_group', 'zuivere_c')->value('id')),
        ];
    }

    public function productRules(): array
    {
        $stored = Setting::get('shipping_rate_product_rules');

        if ($stored !== null && $stored !== '') {
            $decoded = json_decode((string) $stored, true);

            return is_array($decoded) ? $this->normalizeRules($decoded) : [];
        }

        return $this->legacyProductRules();
    }

    public function saveProductRules(array $rules): void
    {
        Setting::set('shipping_rate_product_rules', json_encode(
            $this->normalizeRules($rules),
            JSON_THROW_ON_ERROR
        ));
    }

    public function ruleForProduct(int $productId): ?array
    {
        return collect($this->productRules())->firstWhere('product_id', $productId);
    }

    public function saveProductRule(
        int $productId,
        bool $enabled,
        array $delivery = [],
        array $pickup = [],
    ): void {
        $rules = collect($this->productRules())
            ->reject(fn (array $rule) => $rule['product_id'] === $productId);

        if ($enabled) {
            $rules->push([
                'product_id' => $productId,
                'delivery' => $delivery,
                'pickup' => $pickup,
            ]);
        }

        $this->saveProductRules($rules->values()->all());
    }

    public function priceForProduct(
        int $productId,
        string $fulfillmentMethod,
        int $quantity,
        float $fallback,
    ): float {
        $productRule = collect($this->productRules())
            ->firstWhere('product_id', $productId);

        if (! $productRule) {
            return round($fallback, 2);
        }

        $price = collect($productRule[$fulfillmentMethod] ?? [])
            ->sortBy('quantity')
            ->filter(fn (array $tier) => $quantity >= $tier['quantity'])
            ->pluck('price')
            ->last();

        return $price === null ? round($fallback, 2) : round((float) $price, 2);
    }

    public function priceFor(
        ?string $rateGroup,
        string $fulfillmentMethod,
        int $quantity,
        float $fallback,
    ): float {
        $tierKeys = match ($rateGroup.'_'.$fulfillmentMethod) {
            'gtl_delivery' => [
                ['shipping_rates_gtl_delivery_quantity_3', 'shipping_rates_gtl_delivery_3'],
                ['shipping_rates_gtl_delivery_quantity_5', 'shipping_rates_gtl_delivery_5'],
                ['shipping_rates_gtl_delivery_quantity_10', 'shipping_rates_gtl_delivery_10'],
            ],
            'gtl_pickup' => [
                ['shipping_rates_gtl_pickup_quantity_3', 'shipping_rates_gtl_pickup_3'],
                ['shipping_rates_gtl_pickup_quantity_5', 'shipping_rates_gtl_pickup_5'],
                ['shipping_rates_gtl_pickup_quantity_10', 'shipping_rates_gtl_pickup_10'],
            ],
            'zuivere_c_delivery' => [
                ['shipping_rates_c_delivery_quantity_5', 'shipping_rates_c_delivery_5'],
                ['shipping_rates_c_delivery_quantity_10', 'shipping_rates_c_delivery_10'],
            ],
            'zuivere_c_pickup' => [
                ['shipping_rates_c_pickup_quantity_3', 'shipping_rates_c_pickup_3'],
                ['shipping_rates_c_pickup_quantity_5', 'shipping_rates_c_pickup_5'],
            ],
            default => [],
        };

        $values = $this->values();
        $eligibleKey = collect($tierKeys)
            ->map(fn (array $keys) => [
                'minimum' => (int) $values[$keys[0]],
                'price_key' => $keys[1],
            ])
            ->sortBy('minimum')
            ->filter(fn (array $tier) => $quantity >= $tier['minimum'])
            ->pluck('price_key')
            ->last();

        if (! $eligibleKey) {
            return round($fallback, 2);
        }

        return round((float) $values[$eligibleKey], 2);
    }

    private function normalizeRules(array $rules): array
    {
        return collect($rules)
            ->filter(fn ($rule) => is_array($rule) && ! empty($rule['product_id']))
            ->map(function (array $rule) {
                return [
                    'product_id' => (int) $rule['product_id'],
                    'delivery' => $this->normalizeTiers($rule['delivery'] ?? []),
                    'pickup' => $this->normalizeTiers($rule['pickup'] ?? []),
                ];
            })
            ->unique('product_id')
            ->values()
            ->all();
    }

    private function normalizeTiers(array $tiers): array
    {
        return collect($tiers)
            ->filter(fn ($tier) => is_array($tier) && isset($tier['quantity'], $tier['price']))
            ->map(fn (array $tier) => [
                'quantity' => max(1, (int) $tier['quantity']),
                'price' => round(max(0, (float) $tier['price']), 2),
            ])
            ->sortBy('quantity')
            ->unique('quantity')
            ->values()
            ->all();
    }

    private function legacyProductRules(): array
    {
        $values = $this->values();
        $ids = $this->selectedProductIds();
        $rules = [];

        if ($ids['gtl']) {
            $rules[] = [
                'product_id' => $ids['gtl'],
                'delivery' => $this->legacyTiers($values, 'gtl', 'delivery', [3, 5, 10]),
                'pickup' => $this->legacyTiers($values, 'gtl', 'pickup', [3, 5, 10]),
            ];
        }

        if ($ids['c']) {
            $rules[] = [
                'product_id' => $ids['c'],
                'delivery' => $this->legacyTiers($values, 'c', 'delivery', [5, 10]),
                'pickup' => $this->legacyTiers($values, 'c', 'pickup', [3, 5]),
            ];
        }

        return $rules;
    }

    private function legacyTiers(array $values, string $group, string $method, array $suffixes): array
    {
        return collect($suffixes)->map(fn (int $suffix) => [
            'quantity' => (int) $values["shipping_rates_{$group}_{$method}_quantity_{$suffix}"],
            'price' => (float) $values["shipping_rates_{$group}_{$method}_{$suffix}"],
        ])->all();
    }
}
