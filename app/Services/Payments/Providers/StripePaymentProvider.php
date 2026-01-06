<?php

namespace App\Services\Payments\Providers;

use App\Models\Payment;
use App\Services\Payments\PaymentProvider;
use Illuminate\Support\Str;

class StripePaymentProvider implements PaymentProvider
{
    public function createDeferredPayment(Payment $payment): Payment
    {
        // Placeholder: integrate Stripe Payment Links here.
        $payment->provider = 'stripe';
        $payment->provider_payment_id = $payment->provider_payment_id ?: (string) Str::uuid();
        $payment->pay_link = $payment->pay_link ?: '#';

        return $payment;
    }
}
