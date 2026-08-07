<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_bundle_uses_fixed_price_free_shipping_and_order_snapshots(): void
    {
        Mail::fake();
        $category = Category::create(['name' => 'Actietest', 'slug' => 'actietest']);
        $heater = Product::create(['category_id' => $category->id, 'name' => 'Actiekachel', 'slug' => 'actiekachel', 'price' => 579, 'active' => true]);
        $liquid = Product::create(['category_id' => $category->id, 'name' => 'Actievloeistof', 'slug' => 'actievloeistof', 'price' => 65, 'active' => true, 'rate_group' => 'gtl']);
        $pump = Product::create(['category_id' => $category->id, 'name' => 'Actiepomp', 'slug' => 'actiepomp', 'price' => 5, 'active' => true]);
        $promotion = Promotion::create([
            'main_product_id' => $heater->id,
            'name' => 'Complete testbundel',
            'slug' => 'complete-testbundel',
            'title' => 'Complete testbundel',
            'fixed_price' => 550,
            'free_shipping' => true,
            'show_home' => true,
            'show_product' => true,
            'show_cart' => true,
            'active' => true,
        ]);
        $promotion->items()->createMany([
            ['product_id' => $liquid->id, 'quantity' => 1, 'role' => 'included', 'label' => '20L vloeistof'],
            ['product_id' => $pump->id, 'quantity' => 1, 'role' => 'free', 'label' => 'Gratis pomp'],
        ]);

        $this->post(route('cart.add-promotion', $promotion))->assertRedirect(route('cart.index'));
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Complete testbundel')
            ->assertSee('Gratis standaardverzending');

        $response = $this->post(route('checkout.store'), [
            'name' => 'Actieklant',
            'email' => 'actie@example.nl',
            'address' => 'Teststraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasNoErrors();
        $order = Order::with('items')->firstOrFail();
        $this->assertSame(OrderStatus::PENDING, $order->status);
        $this->assertSame('550.00', $order->total);
        $this->assertSame('0.00', $order->shipping_cost);
        $this->assertCount(3, $order->items);
        $this->assertSame('Complete testbundel', $order->items->first()->promotion_name);
        $this->assertSame('0.00', $order->items->last()->price);
    }

    public function test_expired_bundle_cannot_be_added(): void
    {
        $category = Category::create(['name' => 'Verlopen', 'slug' => 'verlopen']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Product', 'slug' => 'product', 'price' => 10, 'active' => true]);
        $promotion = Promotion::create([
            'main_product_id' => $product->id,
            'name' => 'Verlopen actie',
            'slug' => 'verlopen-actie',
            'title' => 'Verlopen actie',
            'fixed_price' => 5,
            'active' => true,
            'ends_at' => now()->subMinute(),
        ]);

        $this->post(route('cart.add-promotion', $promotion))->assertNotFound();
    }
}
