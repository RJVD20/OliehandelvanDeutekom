<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendPaymentReminderJob;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Http\Request;

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

        $query = Payment::query()->with('order');

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

    public function remind(Payment $payment)
    {
        if ($payment->status !== PaymentStatus::OPEN) {
            return back()->with('toast', 'Betaling is niet openstaand');
        }

        SendPaymentReminderJob::dispatch($payment, 'email');

        PaymentEvent::create([
            'payment_id' => $payment->id,
            'type'       => 'admin_reminder',
            'source'     => 'admin',
            'actor_id'   => auth()->id(),
            'data'       => ['channel' => 'email'],
        ]);

        return back()->with('toast', 'Herinnering verstuurd');
    }

    public function markPaid(Payment $payment)
    {
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
}
