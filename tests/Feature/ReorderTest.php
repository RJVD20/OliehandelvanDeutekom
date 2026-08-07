<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reordering_fills_the_cart_without_creating_an_order(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Actueel product',
            'slug' => 'actueel-product',
            'price' => 42.50,
            'active' => true,
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'status' => OrderStatus::COMPLETED,
            'fulfillment_method' => 'delivery',
            'delivery_service' => 'standard',
            'total' => 80,
            'name' => $user->name,
            'email' => $user->email,
            'address' => 'Teststraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Oude productnaam',
            'price' => 40,
            'quantity' => 2,
        ]);

        $this->actingAs($user)
            ->post(route('account.orders.reorder', $order))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('cart.'.$product->id, [
                'name' => 'Actueel product',
                'price' => 42.50,
                'quantity' => 2,
            ]);

        $this->assertSame(1, Order::count());
    }
}
