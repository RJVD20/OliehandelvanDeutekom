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
            $table->string('province')->nullable()->after('city');
            $table->date('route_date')->nullable()->after('province');
            $table->unsignedSmallInteger('route_sequence')->nullable()->after('route_date');

            $table->index('province');
            $table->index(['route_date', 'province']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('province')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['province']);
            $table->dropIndex(['route_date', 'province']);

            $table->dropColumn(['province', 'route_date', 'route_sequence']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('province');
        });
    }
};
