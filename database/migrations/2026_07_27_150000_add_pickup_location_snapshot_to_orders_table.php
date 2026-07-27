<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'pickup_location_name')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('pickup_location_name')->nullable()->after('pickup_location_id');
            });
        }

        if (! Schema::hasColumn('orders', 'pickup_location_address')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('pickup_location_address')->nullable()->after('pickup_location_name');
            });
        }

        if (! Schema::hasColumn('orders', 'pickup_location_opening')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('pickup_location_opening')->nullable()->after('pickup_location_address');
            });
        }
    }

    public function down(): void
    {
        foreach (['pickup_location_opening', 'pickup_location_address', 'pickup_location_name'] as $column) {
            if (Schema::hasColumn('orders', $column)) {
                Schema::table('orders', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
