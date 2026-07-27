<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Location;
use App\Services\CartPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTierPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_uses_current_delivery_and_pickup_tiers(): void
    {
        $product = $this->product('gtl', 70);
        $cart = [
            $product->id => [
                'name' => $product->name,
                'price' => 70,
                'quantity' => 3,
            ],
        ];

        $pricing = app(CartPricing::class);

        $this->assertSame(62.50, $pricing->calculate($cart, 'delivery')[$product->id]['price']);
        $this->assertSame(57.50, $pricing->calculate($cart, 'pickup')[$product->id]['price']);

        Setting::set('shipping_rates_gtl_delivery_3', '61.25');

        $this->assertSame(61.25, $pricing->calculate($cart, 'delivery')[$product->id]['price']);

        Setting::set('shipping_rates_gtl_delivery_quantity_3', '4');

        $this->assertSame(70.0, $pricing->calculate($cart, 'delivery')[$product->id]['price']);
    }

    public function test_quantity_below_first_tier_keeps_product_base_price(): void
    {
        $product = $this->product('zuivere_c', 49);
        $cart = [
            $product->id => [
                'name' => $product->name,
                'price' => 1,
                'quantity' => 2,
            ],
        ];

        $priced = app(CartPricing::class)->calculate($cart, 'delivery');

        $this->assertSame(49.0, $priced[$product->id]['price']);
        $this->assertFalse($priced[$product->id]['tier_applied']);
    }

    public function test_checkout_stores_the_recalculated_tier_price_and_fulfillment_method(): void
    {
        config([
            'payments.provider' => 'mock',
            'payments.provider_options.mock.base_url' => 'https://example.test',
        ]);
        $product = $this->product('gtl', 70);
        $location = Location::create([
            'name' => 'Depot Utrecht',
            'slug' => 'depot-utrecht',
            'street' => 'Industrieweg 10',
            'postcode_city' => '1234 AB Utrecht',
            'opening' => 'Ma-vr 08:00-17:00',
        ]);

        $this->withSession([
            'fulfillment_method' => 'pickup',
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'price' => 1,
                    'quantity' => 3,
                ],
            ],
        ])->post(route('checkout.store'), [
            'name' => 'Staffelklant',
            'email' => 'staffel@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'payment_method' => 'ideal',
            'pickup_location_id' => $location->id,
        ])->assertRedirectContains('/pay/mock/');

        $order = Order::firstOrFail();

        $this->assertSame('pickup', $order->fulfillment_method);
        $this->assertSame($location->id, $order->pickup_location_id);
        $this->assertSame('Depot Utrecht', $order->pickup_location_name);
        $this->assertSame('Industrieweg 10, 1234 AB Utrecht', $order->pickup_location_address);
        $this->assertEquals(172.50, $order->total);
        $this->assertEquals(57.50, $order->items->first()->price);
    }

    public function test_pickup_checkout_requires_an_existing_depot(): void
    {
        $product = $this->product('gtl', 70);

        $this->withSession([
            'fulfillment_method' => 'pickup',
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'price' => 70,
                    'quantity' => 3,
                ],
            ],
        ])->from(route('checkout.index'))->post(route('checkout.store'), [
            'name' => 'Afhaalklant',
            'email' => 'afhalen@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'payment_method' => 'ideal',
        ])->assertRedirect(route('checkout.index'))
            ->assertSessionHasErrors('pickup_location_id');

        $this->assertDatabaseCount('orders', 0);
    }

    private function product(string $rateGroup, float $price): Product
    {
        $category = Category::create([
            'name' => 'Vloeistoffen '.$rateGroup,
            'slug' => 'vloeistoffen-'.$rateGroup,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Testproduct '.$rateGroup,
            'slug' => 'testproduct-'.$rateGroup,
            'price' => $price,
            'type' => 'vloeistof',
            'rate_group' => $rateGroup,
            'active' => true,
        ]);
    }
}
