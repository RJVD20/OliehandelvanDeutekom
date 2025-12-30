<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedSmallInteger('route_travel_minutes')->nullable()->after('route_sequence');
            $table->unsignedSmallInteger('route_stop_minutes')->nullable()->after('route_travel_minutes');
            $table->text('route_notes')->nullable()->after('route_stop_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['route_travel_minutes', 'route_stop_minutes', 'route_notes']);
        });
    }
};
