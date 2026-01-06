<?php

namespace App\Services\Payments\Providers;

use App\Models\Payment;
use App\Services\Payments\PaymentProvider;
use Illuminate\Support\Str;

class MolliePaymentProvider implements PaymentProvider
{
    public function createDeferredPayment(Payment $payment): Payment
    {
        // Placeholder: integrate Mollie API here. Keep idempotent by reusing provider_payment_id when present.
        $payment->provider = 'mollie';
        $payment->provider_payment_id = $payment->provider_payment_id ?: (string) Str::uuid();
        $payment->pay_link = $payment->pay_link ?: '#';

        return $payment;
    }
}
