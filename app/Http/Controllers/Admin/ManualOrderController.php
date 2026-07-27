<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Payments\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManualOrderController extends Controller
{
    public function create(): View
    {
        $products = Product::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        return view('admin.orders.create', [
            'products' => $products,
            'provinces' => nl_provinces(),
        ]);
    }

    public function store(Request $request, PaymentService $paymentService): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'required_if:send_confirmation,1', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'regex:/^[1-9][0-9]{3}\s?[A-Z]{2}$/i'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', Rule::in(nl_provinces())],
            'notes' => ['nullable', 'string', 'max:2000'],
            'payment_handling' => ['required', Rule::in(['paid', 'pay_on_delivery', 'payment_link'])],
            'payment_method' => ['nullable', Rule::in(array_keys(config('payments.methods', [])))],
            'send_confirmation' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ], [
            'email.required_if' => 'Een e-mailadres is verplicht als de bevestigingsmail wordt verstuurd.',
            'items.*.product_id.distinct' => 'Een product kan maar één keer worden toegevoegd. Pas hiervoor het aantal aan.',
        ]);

        $productIds = collect($data['items'])->pluck('product_id');
        $products = Product::query()
            ->where('active', true)
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        if ($products->count() !== $productIds->unique()->count()) {
            return back()->withInput()->withErrors([
                'items' => 'Een van de gekozen producten is niet meer beschikbaar.',
            ]);
        }

        $postcode = strtoupper(str_replace(' ', '', $data['postcode']));
        $postcode = substr($postcode, 0, 4).' '.substr($postcode, 4);

        [$order, $payment] = DB::transaction(function () use ($data, $products, $postcode) {
            $cart = collect($data['items'])->mapWithKeys(function (array $item) use ($products) {
                $product = $products->get((int) $item['product_id']);

                return [$product->id => [
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => (int) $item['quantity'],
                ]];
            })->all();

            $order = Order::createFromCart($cart, [
                'user_id' => null,
                'source' => 'manual',
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'address' => $data['address'],
                'postcode' => $postcode,
                'city' => $data['city'],
                'province' => $data['province'],
                'route_notes' => $data['notes'] ?? null,
            ]);

            $isPaid = $data['payment_handling'] === 'paid';
            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => $data['payment_handling'] === 'payment_link'
                    ? config('payments.provider', 'mock')
                    : 'manual',
                'status' => $isPaid ? PaymentStatus::PAID : PaymentStatus::OPEN,
                'amount' => $order->total,
                'currency' => 'EUR',
                'due_date' => now()->addDays(14),
                'paid_at' => $isPaid ? now() : null,
                'reminder_count' => 0,
                'meta' => [
                    'payment_method' => $data['payment_method'] ?? null,
                    'handling' => $data['payment_handling'],
                    'created_by_admin_id' => auth()->id(),
                ],
            ]);

            return [$order, $payment];
        });

        $warnings = [];

        if ($data['payment_handling'] === 'payment_link') {
            try {
                $paymentService->ensurePayLink($payment);
            } catch (\Throwable $exception) {
                Log::error('Payment link for manual order could not be created.', [
                    'order_id' => $order->id,
                    'exception' => $exception,
                ]);
                $warnings[] = 'de betaallink kon niet worden aangemaakt';
            }
        }

        if ($request->boolean('send_confirmation') && $order->email) {
            try {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            } catch (\Throwable $exception) {
                Log::error('Manual order confirmation email could not be sent.', [
                    'order_id' => $order->id,
                    'exception' => $exception,
                ]);
                $warnings[] = 'de bevestigingsmail kon niet worden verstuurd';
            }
        }

        $message = 'Handmatige bestelling #'.$order->id.' is aangemaakt.';
        if ($warnings !== []) {
            $message .= ' Let op: '.implode(' en ', $warnings).'.';
        }

        return redirect()->route('admin.orders.show', $order)->with('toast', $message);
    }
}
