<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_an_execution_note_to_an_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::create([
            'status' => OrderStatus::PENDING,
            'total' => 25,
            'name' => 'Testklant',
            'email' => 'klant@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.notes', $order), [
                'route_notes' => 'Voor levering eerst bellen.',
            ])
            ->assertRedirect()
            ->assertSessionHas('toast');

        $this->assertSame('Voor levering eerst bellen.', $order->fresh()->route_notes);
    }
}
