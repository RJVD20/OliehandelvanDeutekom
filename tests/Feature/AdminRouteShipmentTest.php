<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\OrderShippedMail;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminRouteShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_the_shipping_email_to_all_orders_in_a_route(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $route = DeliveryRoute::create([
            'name' => 'Route Noord',
            'route_date' => now()->addDay(),
            'admin_id' => $admin->id,
        ]);

        $firstOrder = $this->createOrder($route, 'eerste@example.nl');
        $secondOrder = $this->createOrder($route, 'tweede@example.nl');
        $otherOrder = $this->createOrder(null, 'andere@example.nl');

        $this->actingAs($admin)
            ->post(route('admin.routes.ship', $route))
            ->assertRedirect()
            ->assertSessionHas('toast', 'Verzendmail verstuurd naar 2 bestelling(en).');

        $this->assertSame(OrderStatus::SHIPPED, $firstOrder->fresh()->status);
        $this->assertSame(OrderStatus::SHIPPED, $secondOrder->fresh()->status);
        $this->assertSame(OrderStatus::PENDING, $otherOrder->fresh()->status);

        Mail::assertSent(OrderShippedMail::class, 2);
        Mail::assertSent(OrderShippedMail::class, fn (OrderShippedMail $mail) => $mail->order->is($firstOrder));
        Mail::assertSent(OrderShippedMail::class, fn (OrderShippedMail $mail) => $mail->order->is($secondOrder));
    }

    public function test_order_without_email_is_marked_as_shipped_without_sending_mail(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $route = DeliveryRoute::create([
            'name' => 'Route Zuid',
            'route_date' => now()->addDay(),
        ]);
        $order = $this->createOrder($route, null);

        $this->actingAs($admin)
            ->post(route('admin.routes.ship', $route))
            ->assertRedirect()
            ->assertSessionHas(
                'toast',
                'Verzendmail verstuurd naar 0 bestelling(en). 1 bestelling(en) hadden geen e-mailadres, maar zijn wel als verzonden gemarkeerd.'
            );

        $this->assertSame(OrderStatus::SHIPPED, $order->fresh()->status);
        Mail::assertNothingSent();
    }

    private function createOrder(?DeliveryRoute $route, ?string $email): Order
    {
        return Order::create([
            'status' => OrderStatus::PENDING,
            'total' => 25,
            'name' => 'Testklant',
            'email' => $email,
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'delivery_route_id' => $route?->id,
        ]);
    }
}
