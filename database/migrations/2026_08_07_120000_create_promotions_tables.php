<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->decimal('fixed_price', 10, 2);
            $table->boolean('free_shipping')->default(false);
            $table->boolean('show_home')->default(true);
            $table->boolean('show_product')->default(true);
            $table->boolean('show_cart')->default(true);
            $table->boolean('active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['active', 'starts_at', 'ends_at']);
        });

        Schema::create('promotion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('role')->default('included');
            $table->string('label')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['promotion_id', 'product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('promotion_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->string('promotion_name')->nullable()->after('product_name');
            $table->json('promotion_meta')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promotion_id');
            $table->dropColumn(['promotion_name', 'promotion_meta']);
        });
        Schema::dropIfExists('promotion_items');
        Schema::dropIfExists('promotions');
    }
};
