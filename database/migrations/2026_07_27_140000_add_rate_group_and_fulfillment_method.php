<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('rate_group', 30)->nullable()->after('type')->index();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('fulfillment_method', 20)->default('delivery')->after('source')->index();
        });

        DB::table('products')
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%gtl%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%turboheating%']);
            })
            ->update(['rate_group' => 'gtl']);

        DB::table('products')
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%zuivere c%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%petroleum c%']);
            })
            ->update(['rate_group' => 'zuivere_c']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['fulfillment_method']);
            $table->dropColumn('fulfillment_method');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['rate_group']);
            $table->dropColumn('rate_group');
        });
    }
};
