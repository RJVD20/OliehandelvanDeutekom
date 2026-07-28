<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRouteLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_aggregated_route_inventory_with_stop_distribution(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->product();
        $route = DeliveryRoute::create([
            'name' => 'Vrijdagroute',
            'route_date' => now()->addDay(),
        ]);

        $this->addOrder($route, $product, 1, 2, 'Eerste klant');
        $this->addOrder($route, $product, 2, 3, 'Tweede klant');

        $this->actingAs($admin)
            ->get(route('admin.routes.loading', $route))
            ->assertOk()
            ->assertSee('Wagen laden')
            ->assertSee('Testjerrycan')
            ->assertSee('5×')
            ->assertSee('Stop 1')
            ->assertSee('Stop 2')
            ->assertSee('Print paklijst');
    }

    public function test_admin_can_persist_a_loaded_inventory_item(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->product();
        $route = DeliveryRoute::create([
            'name' => 'Laadstatusroute',
            'route_date' => now()->addDay(),
        ]);
        $this->addOrder($route, $product, 1, 4, 'Klant');

        $this->actingAs($admin)
            ->patchJson(route('admin.routes.loading.toggle', $route), [
                'item_key' => 'product:'.$product->id,
                'loaded' => true,
            ])
            ->assertOk()
            ->assertJson([
                'checked_count' => 1,
                'total_count' => 1,
                'loaded_quantity' => 4,
                'total_quantity' => 4,
            ]);

        $this->assertSame(
            ['product:'.$product->id],
            $route->fresh()->loading_checked_items
        );
    }

    private function product(): Product
    {
        $category = Category::create([
            'name' => 'Laadproducten',
            'slug' => 'laadproducten',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Testjerrycan',
            'slug' => 'testjerrycan',
            'price' => 25,
            'image' => 'products/testjerrycan.webp',
            'active' => true,
        ]);
    }

    private function addOrder(
        DeliveryRoute $route,
        Product $product,
        int $sequence,
        int $quantity,
        string $customer,
    ): Order {
        $order = Order::createFromCart([
            $product->id => [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ],
        ], [
            'name' => $customer,
            'email' => strtolower(str_replace(' ', '', $customer)).'@example.nl',
            'address' => 'Teststraat '.$sequence,
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'fulfillment_method' => 'delivery',
        ]);

        $order->update([
            'delivery_route_id' => $route->id,
            'route_date' => $route->route_date,
            'route_sequence' => $sequence,
        ]);

        return $order;
    }
}
