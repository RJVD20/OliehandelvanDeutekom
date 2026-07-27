<?php

namespace Tests\Unit;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    public function test_payment_handling_has_a_clear_label(): void
    {
        $payment = new Payment([
            'status' => PaymentStatus::OPEN,
            'pay_link' => 'https://example.test/betalen',
            'meta' => ['handling' => 'payment_link'],
        ]);
        $payment->setRelation('order', new Order(['email' => 'klant@example.nl']));

        $this->assertSame('Online betaallink', $payment->handlingLabel());

        $payment->pay_link = null;
        $payment->meta = ['handling' => 'pay_on_delivery'];

        $this->assertSame('Betalen bij levering', $payment->handlingLabel());
    }
}
