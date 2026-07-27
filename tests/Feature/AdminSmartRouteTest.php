<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminSmartRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_preview_and_confirm_a_smart_route(): void
    {
        config(['services.mapbox.token' => 'test-token']);
        Http::fake([
            'api.mapbox.com/optimized-trips/*' => Http::response([
                'waypoints' => [
                    ['waypoint_index' => 1],
                    ['waypoint_index' => 0],
                ],
                'trips' => [['duration' => 1800]],
            ]),
        ]);

        $admin = User::factory()->create(['is_admin' => true]);
        [$first, $second] = $this->ordersWithCoordinates();

        $payload = [
            'route_date' => now()->addDay()->toDateString(),
            'name' => 'Slimme testroute',
            'admin_user_id' => $admin->id,
            'order_ids' => [$first->id, $second->id],
        ];

        $this->actingAs($admin)
            ->post(route('admin.routes.smart.preview'), $payload)
            ->assertOk()
            ->assertSee('Voorgestelde volgorde')
            ->assertSee('Slimme testroute');

        $this->actingAs($admin)
            ->post(route('admin.routes.smart.store'), [
                ...$payload,
                'order_ids' => [$second->id, $first->id],
            ])
            ->assertRedirect();

        $first->refresh();
        $second->refresh();

        $this->assertNotNull($first->delivery_route_id);
        $this->assertSame($first->delivery_route_id, $second->delivery_route_id);
        $this->assertSame(2, $first->route_sequence);
        $this->assertSame(1, $second->route_sequence);
        $this->assertSame($admin->id, $first->assigned_admin_id);
    }

    public function test_an_order_cannot_be_added_to_two_routes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        [$order] = $this->ordersWithCoordinates(1);

        $order->update(['route_date' => now()->addDay()]);

        $this->actingAs($admin)
            ->post(route('admin.routes.smart.store'), [
                'route_date' => now()->addDay()->toDateString(),
                'name' => 'Dubbele route',
                'order_ids' => [$order->id],
            ])
            ->assertStatus(409);

        $this->assertDatabaseMissing('delivery_routes', ['name' => 'Dubbele route']);
    }

    private function ordersWithCoordinates(int $count = 2): array
    {
        $category = Category::create(['name' => 'Routeproducten', 'slug' => 'routeproducten']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Jerrycan',
            'slug' => 'jerrycan',
            'price' => 20,
            'active' => true,
        ]);

        return collect(range(1, $count))->map(function (int $index) use ($product) {
            $order = Order::createFromCart([
                $product->id => ['name' => $product->name, 'price' => $product->price, 'quantity' => 1],
            ], [
                'name' => "Klant {$index}",
                'email' => "klant{$index}@example.nl",
                'address' => "Dorpsstraat {$index}",
                'postcode' => '1234 AB',
                'city' => 'Utrecht',
                'province' => 'Utrecht',
            ]);

            $hash = hash('sha256', strtolower(trim(
                $order->address.'|'.$order->postcode.'|'.$order->city.'|'.$order->province
            )));

            $order->update([
                'geo_lat' => 52.09 + ($index / 100),
                'geo_lng' => 5.11 + ($index / 100),
                'geo_address_hash' => $hash,
            ]);

            return $order->fresh();
        })->all();
    }
}
