<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Mail\ManualPaymentRequestMail;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status'     => ['nullable', 'in:open,paid,expired,failed,cancelled'],
            'due_before' => ['nullable', 'date'],
            'due_after'  => ['nullable', 'date'],
            'soon'       => ['nullable', 'boolean'],
        ]);

        $query = Payment::query()
            ->with('order')
            ->whereHas('order', fn ($query) => $query->placed());

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['due_before'] ?? null) {
            $query->whereDate('due_date', '<=', $filters['due_before']);
        }
        if ($filters['due_after'] ?? null) {
            $query->whereDate('due_date', '>=', $filters['due_after']);
        }

        if ($request->boolean('soon')) {
            $query->whereDate('due_date', '<=', now()->addDays(3));
        }

        $payments = $query
            ->orderBy('status')
            ->orderBy('due_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', compact('payments', 'filters'));
    }

    public function markPaid(Payment $payment)
    {
        abort_if($payment->order?->isAwaitingPayment(), 404);

        if ($payment->status === PaymentStatus::PAID) {
            return back()->with('toast', 'Al betaald');
        }

        $old = $payment->status;
        $payment->status = PaymentStatus::PAID;
        $payment->paid_at = now();
        $payment->save();

        PaymentEvent::create([
            'payment_id' => $payment->id,
            'type'       => 'admin_override',
            'source'     => 'admin',
            'actor_id'   => auth()->id(),
            'data'       => ['from' => $old->value ?? (string) $old, 'to' => PaymentStatus::PAID->value],
        ]);

        return back()->with('toast', 'Gemarkeerd als betaald');
    }

    public function sendPaymentRequest(Payment $payment)
    {
        abort_unless($payment->canSendManualPaymentRequest(), 422);

        try {
            Mail::to($payment->order->email)->send(new ManualPaymentRequestMail($payment));
        } catch (\Throwable $exception) {
            Log::error('Manual payment request could not be sent.', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'exception' => $exception,
            ]);

            return back()->with('toast', 'Het betaalverzoek kon niet worden verstuurd.');
        }

        PaymentEvent::create([
            'payment_id' => $payment->id,
            'type' => 'manual_payment_request',
            'source' => 'admin',
            'actor_id' => auth()->id(),
            'data' => ['recipient' => $payment->order->email],
        ]);

        return back()->with('toast', 'Betaalverzoek verstuurd naar '.$payment->order->email.'.');
    }
}
