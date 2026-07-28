<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Mail\ManualPaymentRequestMail;
use App\Models\AuditLog;
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
            'search' => ['nullable', 'string', 'max:100'],
            'tab' => ['nullable', 'in:all,open,overdue,soon,paid,failed'],
            'handling' => ['nullable', 'in:online,payment_link,pay_on_delivery,bank_transfer,manual'],
            'due_before' => ['nullable', 'date'],
            'due_after'  => ['nullable', 'date'],
        ]);

        $query = Payment::query()
            ->with(['order', 'events.actor'])
            ->whereHas('order', fn ($query) => $query->placed());

        $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('provider_payment_id', 'like', "%{$search}%")
                        ->orWhereHas('order', function ($order) use ($search) {
                            $order
                                ->where('id', ctype_digit($search) ? (int) $search : 0)
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['due_before'] ?? null, fn ($q, $date) => $q->whereDate('due_date', '<=', $date))
            ->when($filters['due_after'] ?? null, fn ($q, $date) => $q->whereDate('due_date', '>=', $date))
            ->when($filters['handling'] ?? null, function ($query, $handling) {
                match ($handling) {
                    'pay_on_delivery', 'bank_transfer' => $query->where('meta->handling', $handling),
                    'payment_link' => $query->whereNotNull('pay_link'),
                    'online' => $query->where('provider', '!=', 'manual'),
                    'manual' => $query->where('provider', 'manual'),
                };
            })
            ->when(($filters['tab'] ?? 'all') === 'open', fn ($q) => $q->where('status', PaymentStatus::OPEN))
            ->when(($filters['tab'] ?? 'all') === 'overdue', fn ($q) => $this->overdue($q))
            ->when(($filters['tab'] ?? 'all') === 'soon', fn ($q) => $this->dueSoon($q))
            ->when(($filters['tab'] ?? 'all') === 'paid', fn ($q) => $q->where('status', PaymentStatus::PAID))
            ->when(($filters['tab'] ?? 'all') === 'failed', fn ($q) => $q->whereIn('status', [
                PaymentStatus::FAILED,
                PaymentStatus::EXPIRED,
                PaymentStatus::CANCELLED,
            ]));

        $payments = $query
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 WHEN status = 'failed' THEN 1 WHEN status = 'expired' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $base = Payment::query()->whereHas('order', fn ($query) => $query->placed());
        $stats = [
            'open_amount' => (clone $base)->where('status', PaymentStatus::OPEN)->sum('amount'),
            'overdue_count' => $this->overdue(clone $base)->count(),
            'overdue_amount' => $this->overdue(clone $base)->sum('amount'),
            'due_soon_count' => $this->dueSoon(clone $base)->count(),
            'paid_this_month' => (clone $base)
                ->where('status', PaymentStatus::PAID)
                ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'filters', 'stats'));
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
        AuditLog::record(
            'updated',
            'payment',
            $payment->id,
            'Betaling voor bestelling #'.$payment->order_id,
            ['status' => $old->value ?? (string) $old],
            ['status' => PaymentStatus::PAID->value],
        );

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
        AuditLog::record(
            'updated',
            'payment',
            $payment->id,
            'Betaalverzoek voor bestelling #'.$payment->order_id,
            ['betaalverzoek_verstuurd' => null],
            ['betaalverzoek_verstuurd' => now()->toDateTimeString()],
        );

        return back()->with('toast', 'Betaalverzoek verstuurd naar '.$payment->order->email.'.');
    }

    private function overdue($query)
    {
        return $query
            ->where('status', PaymentStatus::OPEN)
            ->whereDate('due_date', '<', today())
            ->where(function ($query) {
                $query
                    ->whereNull('meta->handling')
                    ->orWhere('meta->handling', '!=', 'pay_on_delivery');
            });
    }

    private function dueSoon($query)
    {
        return $query
            ->where('status', PaymentStatus::OPEN)
            ->whereBetween('due_date', [today(), today()->addDays(3)])
            ->where(function ($query) {
                $query
                    ->whereNull('meta->handling')
                    ->orWhere('meta->handling', '!=', 'pay_on_delivery');
            });
    }
}
