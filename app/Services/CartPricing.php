<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Promotion;

class CartPricing
{
    public function __construct(private readonly ShippingRates $rates)
    {
    }

    public function calculate(array $cart, string $fulfillmentMethod = 'delivery'): array
    {
        $method = in_array($fulfillmentMethod, ['delivery', 'pickup'], true)
            ? $fulfillmentMethod
            : 'delivery';

        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $promotionIds = collect($cart)->pluck('promotion_id')->filter()->map(fn ($id) => (int) $id)->unique();
        $promotions = Promotion::query()
            ->currentlyActive()
            ->with(['mainProduct', 'items.product'])
            ->whereIn('id', $promotionIds)
            ->get()
            ->keyBy('id');

        return collect($cart)->mapWithKeys(function (array $item, int|string $productId) use ($products, $promotions, $method) {
            $product = $products->get((int) $productId);
            if (! $product || ! $product->active) {
                return [];
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $basePrice = round((float) $product->price, 2);
            $promotion = isset($item['promotion_id']) ? $promotions->get((int) $item['promotion_id']) : null;

            if ($promotion && (int) $promotion->main_product_id === (int) $product->id) {
                $unitPrice = round((float) $promotion->fixed_price, 2);
                $normalValue = $promotion->normalValue();
                $discountPerUnit = round(max(0, $normalValue - $unitPrice), 2);

                return [$product->id => [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image,
                    'price' => $unitPrice,
                    'base_price' => $normalValue,
                    'quantity' => $quantity,
                    'rate_group' => $product->rate_group,
                    'tier_applied' => false,
                    'discount_per_unit' => $discountPerUnit,
                    'discount_total' => round($discountPerUnit * $quantity, 2),
                    'tier_progress' => null,
                    'promotion_id' => $promotion->id,
                    'promotion_name' => $promotion->name,
                    'promotion_title' => $promotion->title,
                    'promotion_image' => $promotion->imageUrl(),
                    'promotion_items' => $promotion->items->map(fn ($bundleItem) => [
                        'product_id' => $bundleItem->product_id,
                        'name' => $bundleItem->product->name,
                        'quantity' => $bundleItem->quantity,
                        'role' => $bundleItem->role,
                        'label' => $bundleItem->label,
                    ])->all(),
                    'free_shipping' => $promotion->free_shipping,
                ]];
            }
            $unitPrice = $this->rates->priceForProduct(
                $product->id,
                $method,
                $quantity,
                $basePrice,
            );
            $discountPerUnit = round(max(0, $basePrice - $unitPrice), 2);

            return [$product->id => [
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image,
                'price' => $unitPrice,
                'base_price' => $basePrice,
                'quantity' => $quantity,
                'rate_group' => $product->rate_group,
                'tier_applied' => $unitPrice !== $basePrice,
                'discount_per_unit' => $discountPerUnit,
                'discount_total' => round($discountPerUnit * $quantity, 2),
                'tier_progress' => $this->rates->tierProgressForProduct(
                    $product->id,
                    $method,
                    $quantity,
                    $unitPrice,
                ),
            ]];
        })->all();
    }

    public function deliveryCosts(
        array $cart,
        string $fulfillmentMethod = 'delivery',
        string $deliveryService = 'standard',
    ): array {
        if ($fulfillmentMethod !== 'delivery') {
            return ['jerrycans' => 0, 'standard' => 0.0, 'express' => 0.0, 'total' => 0.0];
        }

        $promotionHasFreeShipping = collect($cart)->contains(
            fn (array $item) => ! empty($item['promotion_id']) && ! empty($item['free_shipping'])
        );

        $jerrycans = (int) collect($cart)
            ->filter(fn (array $item) => ! empty($item['rate_group']))
            ->sum('quantity');
        $standard = $promotionHasFreeShipping ? 0.0 : ($jerrycans < 3 ? 5.0 : 0.0);
        $express = $deliveryService === 'express' ? 10.0 : 0.0;
        $total = $deliveryService === 'express' ? $express : $standard;

        return [
            'jerrycans' => $jerrycans,
            'standard' => $standard,
            'express' => $express,
            'total' => $total,
        ];
    }

    public function total(
        array $cart,
        string $fulfillmentMethod = 'delivery',
        string $deliveryService = 'standard',
    ): float
    {
        $products = collect($cart)->sum(
            fn (array $item) => (float) $item['price'] * (int) $item['quantity']
        );

        return round($products + $this->deliveryCosts($cart, $fulfillmentMethod, $deliveryService)['total'], 2);
    }
}
