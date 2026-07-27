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

            return [$product->id => [
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image,
                'price' => $unitPrice,
                'base_price' => $basePrice,
                'quantity' => $quantity,
                'rate_group' => $product->rate_group,
                'tier_applied' => $unitPrice !== $basePrice,
            ]];
        })->all();
    }

    public function total(array $cart): float
    {
        return round(collect($cart)->sum(
            fn (array $item) => (float) $item['price'] * (int) $item['quantity']
        ), 2);
    }
}
