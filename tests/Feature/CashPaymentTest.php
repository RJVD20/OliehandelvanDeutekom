<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CashPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_an_order_to_pay_in_cash(): void
    {
        Mail::fake();
        $product = $this->product();

        $response = $this->withSession([
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ],
            ],
        ])->post(route('checkout.store'), [
            'name' => 'Cash klant',
            'email' => 'cash@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect(route('home'))->assertSessionHasNoErrors();

        $order = Order::firstOrFail();
        $payment = $order->latestPayment;

        $this->assertSame(OrderStatus::PENDING, $order->status);
        $this->assertSame('manual', $payment->provider);
        $this->assertSame(PaymentStatus::OPEN, $payment->status);
        $this->assertTrue($payment->isCashPending());
        $this->assertNull($payment->pay_link);
        $this->assertEmpty(session('cart'));
    }

    public function test_driver_can_check_off_cash_and_action_is_logged(): void
    {
        $driver = User::factory()->create(['is_admin' => true]);
        $route = DeliveryRoute::create([
            'name' => 'Cashroute test',
            'route_date' => today(),
            'admin_id' => $driver->id,
        ]);
        $order = Order::create([
            'status' => OrderStatus::SHIPPED,
            'total' => 25,
            'name' => 'Cash klant',
            'email' => 'cash@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'delivery_route_id' => $route->id,
        ]);
        $payment = $order->payments()->create([
            'provider' => 'manual',
            'status' => PaymentStatus::OPEN,
            'amount' => 25,
            'currency' => 'EUR',
            'meta' => ['payment_method' => 'cash', 'handling' => 'cash_on_delivery'],
        ]);

        $this->actingAs($driver)
            ->post(route('driver.orders.cash-received', $order))
            ->assertRedirect();

        $this->assertSame(PaymentStatus::PAID, $payment->fresh()->status);
        $this->assertNotNull($payment->fresh()->paid_at);
        $this->assertDatabaseHas('payment_events', [
            'payment_id' => $payment->id,
            'type' => 'cash_received',
            'source' => 'driver',
            'actor_id' => $driver->id,
        ]);
    }

    private function product(): Product
    {
        $category = Category::create(['name' => 'Cash test', 'slug' => 'cash-test']);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Cash product',
            'slug' => 'cash-product',
            'price' => 25,
            'active' => true,
        ]);
    }
}
