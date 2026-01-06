<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;

class PaymentService
{
    public function __construct(private readonly PaymentProviderFactory $factory)
    {
    }

    public function ensurePayLink(Payment $payment): Payment
    {
        if ($payment->status !== PaymentStatus::OPEN) {
            return $payment;
        }

        $provider = $this->factory->make(config('payments.provider'));
        $payment = $provider->createDeferredPayment($payment);

        $payment->save();

        return $payment;
    }
}
