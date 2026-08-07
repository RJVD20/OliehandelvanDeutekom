<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manually_complete_a_placed_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::create([
            'status' => OrderStatus::SHIPPED,
            'total' => 25,
            'name' => 'Testklant',
            'email' => 'klant@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.orders.complete', $order))
            ->assertRedirect()
            ->assertSessionHas('toast', 'Bestelling #'.$order->id.' is afgerond.');

        $this->assertSame(OrderStatus::COMPLETED, $order->fresh()->status);
    }

    public function test_admin_cannot_complete_a_cancelled_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::create([
            'status' => OrderStatus::CANCELLED,
            'total' => 25,
            'name' => 'Testklant',
            'email' => 'klant@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.orders.complete', $order))
            ->assertUnprocessable();

        $this->assertSame(OrderStatus::CANCELLED, $order->fresh()->status);
    }
}
