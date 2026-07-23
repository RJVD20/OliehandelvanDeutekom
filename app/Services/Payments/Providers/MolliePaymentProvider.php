<?php

namespace App\Services\Payments\Providers;

use App\Models\Payment;
use App\Services\Payments\PaymentProvider;
use Illuminate\Support\Facades\URL;
use Mollie\Api\MollieApiClient;

class MolliePaymentProvider implements PaymentProvider
{
    public function __construct(private readonly MollieApiClient $mollie) {}

    public function createDeferredPayment(Payment $payment): Payment
    {
        if ($payment->provider_payment_id && $payment->pay_link) {
            return $payment;
        }

        $molliePayment = $this->mollie->payments->create([
            'amount' => [
                'currency' => strtoupper($payment->currency),
                'value' => number_format((float) $payment->amount, 2, '.', ''),
            ],
            'description' => 'Bestelling #'.$payment->order_id,
            'redirectUrl' => URL::temporarySignedRoute(
                'payments.return',
                now()->addDays(14),
                ['payment' => $payment->id],
            ),
            'webhookUrl' => route('payments.webhook', ['provider' => 'mollie']),
            'metadata' => [
                'order_id' => $payment->order_id,
                'payment_id' => $payment->id,
            ],
        ]);

        $payment->provider = 'mollie';
        $payment->provider_payment_id = $molliePayment->id;
        $payment->pay_link = $molliePayment->getCheckoutUrl();
        $payment->meta = array_merge($payment->meta ?? [], [
            'mollie_mode' => $molliePayment->mode,
        ]);

        return $payment;
    }
}
