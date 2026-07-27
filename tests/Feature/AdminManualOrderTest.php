<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Mail\OrderConfirmationMail;
use App\Mail\ManualPaymentRequestMail;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminManualOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_paid_manual_order(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Testcategorie', 'slug' => 'testcategorie']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Testproduct',
            'slug' => 'testproduct',
            'price' => 12.50,
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.store'), [
            'name' => 'Telefonische klant',
            'email' => 'klant@example.nl',
            'phone' => '0612345678',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234ab',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'fulfillment_method' => 'delivery',
            'payment_handling' => 'paid_cash',
            'send_confirmation' => '1',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $order = \App\Models\Order::firstOrFail();

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame('manual', $order->source);
        $this->assertSame('0612345678', $order->phone);
        $this->assertEquals(25.00, $order->total);
        $this->assertSame(2, $order->items->first()->quantity);
        $this->assertSame(PaymentStatus::PAID, $order->latestPayment->status);
        Mail::assertSent(OrderConfirmationMail::class, 1);
    }

    public function test_non_admin_cannot_open_manual_order_form(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.orders.create'))
            ->assertForbidden();
    }

    public function test_admin_can_send_a_payment_request_for_a_manual_order(): void
    {
        Mail::fake();
        config([
            'payments.provider' => 'mock',
            'payments.provider_options.mock.base_url' => 'https://example.test',
        ]);

        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Betaaltest', 'slug' => 'betaaltest']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Betaalproduct',
            'slug' => 'betaalproduct',
            'price' => 30,
            'active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'name' => 'Telefonische klant',
            'email' => 'betalen@example.nl',
            'phone' => '0612345678',
            'address' => 'Dorpsstraat 2',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'fulfillment_method' => 'delivery',
            'payment_handling' => 'payment_link',
            'payment_method' => 'ideal',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ])->assertRedirect();

        $payment = \App\Models\Payment::firstOrFail();

        $this->assertTrue($payment->canSendManualPaymentRequest());

        $this->post(route('admin.payments.send-request', $payment))
            ->assertRedirect();

        Mail::assertSent(ManualPaymentRequestMail::class, function ($mail) {
            return $mail->hasTo('betalen@example.nl');
        });
        $this->assertDatabaseHas('payment_events', [
            'payment_id' => $payment->id,
            'type' => 'manual_payment_request',
            'source' => 'admin',
        ]);
    }
}
