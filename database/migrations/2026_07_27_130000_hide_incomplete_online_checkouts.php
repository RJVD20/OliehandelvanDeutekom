<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('orders')
            ->where('source', 'web')
            ->where('status', OrderStatus::PENDING->value)
            ->whereNull('delivery_route_id')
            ->whereNull('route_date')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('payments')
                    ->whereColumn('payments.order_id', 'orders.id')
                    ->whereIn('payments.provider', ['mollie', 'mock'])
                    ->where('payments.status', '!=', 'paid');
            })
            ->update(['status' => OrderStatus::AWAITING_PAYMENT->value]);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', OrderStatus::AWAITING_PAYMENT->value)
            ->update(['status' => OrderStatus::PENDING->value]);
    }
};
