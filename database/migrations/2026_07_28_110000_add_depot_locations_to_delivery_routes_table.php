<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_routes', function (Blueprint $table) {
            $table->foreignId('start_location_id')->nullable()->after('admin_id')->constrained('locations')->nullOnDelete();
            $table->foreignId('end_location_id')->nullable()->after('start_location_id')->constrained('locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_routes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('end_location_id');
            $table->dropConstrainedForeignId('start_location_id');
        });
    }
};
