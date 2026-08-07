<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $heater = DB::table('products')->where('slug', 'zibro-lc-150')->first();
        $liquid = DB::table('products')->where('slug', 'turboheating-20l-in-nieuwe-jerrycan')->first();
        $pump = DB::table('products')->where('slug', 'normale-hevelpomp')->first();

        if (! $heater || ! $liquid || ! $pump) return;

        DB::table('products')->updateOrInsert(
            ['slug' => 'zibro-lc-150-afstandsbediening'],
            [
                'category_id' => $pump->category_id,
                'name' => 'Zibro LC-150 afstandsbediening',
                'price' => 0,
                'active' => false,
                'featured' => false,
                'type' => 'promotion_component',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        $remote = DB::table('products')->where('slug', 'zibro-lc-150-afstandsbediening')->first();

        DB::table('promotions')->updateOrInsert(
            ['slug' => 'zibro-lc-150-complete-set'],
            [
                'main_product_id' => $heater->id,
                'name' => 'Zibro LC-150 complete actieset',
                'title' => 'Zibro LC-150 complete set',
                'short_description' => 'Zibro LC-150 met 20L TurboHeating kristalvloeistof, gratis hevelpomp, afstandsbediening en standaardverzending.',
                'description' => 'Complete set, direct klaar voor gebruik.',
                'image_path' => 'images/acties/Zibro_LC150.png',
                'image_alt' => 'Aanbieding Zibro LC-150 complete set met TurboHeating vloeistof, hevelpomp en afstandsbediening',
                'fixed_price' => 550,
                'free_shipping' => true,
                'show_home' => true,
                'show_product' => true,
                'show_cart' => true,
                'active' => true,
                'sort_order' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        $promotion = DB::table('promotions')->where('slug', 'zibro-lc-150-complete-set')->first();
        DB::table('promotion_items')->where('promotion_id', $promotion->id)->delete();
        DB::table('promotion_items')->insert([
            ['promotion_id' => $promotion->id, 'product_id' => $liquid->id, 'quantity' => 1, 'role' => 'included', 'label' => '20L TurboHeating kristalvloeistof', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['promotion_id' => $promotion->id, 'product_id' => $pump->id, 'quantity' => 1, 'role' => 'free', 'label' => 'Hevelpomp', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['promotion_id' => $promotion->id, 'product_id' => $remote->id, 'quantity' => 1, 'role' => 'free', 'label' => 'Afstandsbediening', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        $promotion = DB::table('promotions')->where('slug', 'zibro-lc-150-complete-set')->first();
        if ($promotion) DB::table('promotions')->where('id', $promotion->id)->delete();
        DB::table('products')->where('slug', 'zibro-lc-150-afstandsbediening')->delete();
    }
};
