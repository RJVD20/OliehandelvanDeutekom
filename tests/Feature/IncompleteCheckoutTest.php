<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\OrderConfirmationMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment as MolliePayment;
use Tests\TestCase;

class IncompleteCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpaid_checkout_is_not_treated_as_an_order(): void
    {
        Mail::fake();
        config([
            'payments.provider' => 'mock',
            'payments.provider_options.mock.base_url' => 'https://example.test',
        ]);

        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Testproduct',
            'slug' => 'testproduct',
            'price' => 25,
            'active' => true,
        ]);

        $response = $this->withSession([
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ],
            ],
        ])->post(route('checkout.store'), [
            'name' => 'Afgebroken klant',
            'email' => 'afgebroken@example.nl',
            'address' => 'Dorpsstraat 1',
            'postcode' => '1234 AB',
            'city' => 'Utrecht',
            'province' => 'Utrecht',
            'payment_method' => 'ideal',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirectContains('/pay/mock/');
        $order = Order::firstOrFail();

        $this->assertSame(OrderStatus::AWAITING_PAYMENT, $order->status);
        $this->assertNotEmpty(session('cart'));
        $this->assertSame(0, Order::placed()->count());
        Mail::assertNothingSent();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertDontSee('Afgebroken klant');

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertNotFound();

        $payment = $order->latestPayment;
        $payment->update([
            'provider' => 'mollie',
            'provider_payment_id' => 'tr_paid_checkout',
        ]);

        $mollie = new MockMollieClient([
            GetPaymentRequest::class => MockResponse::resource(MolliePayment::class)
                ->with([
                    'id' => 'tr_paid_checkout',
                    'mode' => 'test',
                    'status' => 'paid',
                    'amount' => ['currency' => 'EUR', 'value' => '25.00'],
                ])
                ->create(),
        ]);
        $this->app->instance(MollieApiClient::class, $mollie);

        $this->post(route('payments.webhook', ['provider' => 'mollie']), [
            'id' => 'tr_paid_checkout',
        ])->assertOk();

        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
        $this->assertSame(1, Order::placed()->count());
        Mail::assertSent(OrderConfirmationMail::class, 1);
    }
}
