<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('source', 20)->default('web')->after('status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn(['phone', 'source']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
