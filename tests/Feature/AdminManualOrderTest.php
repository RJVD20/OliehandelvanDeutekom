<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Mail\OrderConfirmationMail;
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
            'payment_handling' => 'paid',
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
}
