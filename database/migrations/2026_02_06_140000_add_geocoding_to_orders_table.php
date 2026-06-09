<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('geo_lat', 10, 7)->nullable()->after('province');
            $table->decimal('geo_lng', 10, 7)->nullable()->after('geo_lat');
            $table->string('geo_address_hash', 64)->nullable()->after('geo_lng');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['geo_lat', 'geo_lng', 'geo_address_hash']);
        });
    }
};
