<?php

namespace App\Services;

use App\Models\Product;

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

        return collect($cart)->mapWithKeys(function (array $item, int|string $productId) use ($products, $method) {
            $product = $products->get((int) $productId);
            if (! $product || ! $product->active) {
                return [];
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $basePrice = round((float) $product->price, 2);
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

        $jerrycans = (int) collect($cart)
            ->filter(fn (array $item) => ! empty($item['rate_group']))
            ->sum('quantity');
        $standard = $jerrycans < 3 ? 5.0 : 0.0;
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
