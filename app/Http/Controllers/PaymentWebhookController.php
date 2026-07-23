<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Http\Request;
use Mollie\Api\MollieApiClient;

class PaymentWebhookController extends Controller
{
    public function __construct(private readonly MollieApiClient $mollie) {}

    public function handle(string $provider, Request $request)
    {
        if ($provider !== 'mollie') {
            return response()->json(['message' => 'unknown provider'], 404);
        }

        $providerPaymentId = $request->input('id') ?? $request->input('payment_id');
        if (! is_string($providerPaymentId) || $providerPaymentId === '') {
            return response()->json(['message' => 'missing id'], 400);
        }

        $payment = Payment::where('provider', 'mollie')
            ->where('provider_payment_id', $providerPaymentId)
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'ok'], 200);
        }

        $this->syncStatus($payment);

        return response()->json(['message' => 'ok'], 200);
    }

    public function returnFromProvider(Payment $payment)
    {
        if ($payment->provider === 'mollie' && $payment->provider_payment_id) {
            $this->syncStatus($payment);
        }

        $message = $payment->fresh()->status === PaymentStatus::PAID
            ? 'Betaling geslaagd. Bedankt voor je bestelling!'
            : 'Je betaling wordt verwerkt. Je ontvangt een bevestiging zodra deze is afgerond.';

        return auth()->check()
            ? redirect()->route('account.orders')->with('toast', $message)
            : redirect()->route('home')->with('toast', $message);
    }

    private function syncStatus(Payment $payment): void
    {
        $molliePayment = $this->mollie->payments->get($payment->provider_payment_id);

        $expectedAmount = number_format((float) $payment->amount, 2, '.', '');
        if (
            strtoupper($molliePayment->amount->currency) !== strtoupper($payment->currency)
            || $molliePayment->amount->value !== $expectedAmount
        ) {
            abort(422, 'Payment amount mismatch.');
        }

        $newStatus = $this->mapStatus($molliePayment->status);
        if ($newStatus && $payment->status !== $newStatus) {
            $oldStatus = $payment->status;
            $payment->status = $newStatus;
            if ($newStatus === PaymentStatus::PAID) {
                $payment->paid_at = now();
            }
            $payment->save();

            PaymentEvent::create([
                'payment_id' => $payment->id,
                'type' => 'webhook_status_update',
                'source' => 'mollie',
                'data' => ['from' => $oldStatus->value ?? (string) $oldStatus, 'to' => $newStatus->value],
            ]);
        }
    }

    private function mapStatus(?string $status): ?PaymentStatus
    {
        return match ($status) {
            'paid', 'succeeded' => PaymentStatus::PAID,
            'expired' => PaymentStatus::EXPIRED,
            'failed' => PaymentStatus::FAILED,
            'canceled', 'cancelled' => PaymentStatus::CANCELLED,
            default => null,
        };
    }
}
