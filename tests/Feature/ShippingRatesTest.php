<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingRatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_shows_existing_staffel_prices(): void
    {
        $category = Category::create(['name' => 'Brandstoffen', 'slug' => 'brandstoffen']);
        $gtl = Product::create([
            'category_id' => $category->id,
            'name' => 'Witte blanke GTL Turboheating – 20 liter',
            'slug' => 'gtl',
            'price' => 70,
            'rate_group' => 'gtl',
            'active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Rode Zuivere C – 20 liter',
            'slug' => 'zuivere-c',
            'price' => 49,
            'rate_group' => 'zuivere_c',
            'active' => true,
        ]);

        $this->get(route('product.show', $gtl->slug))
            ->assertOk()
            ->assertSee('Voordeel bij meerdere stuks')
            ->assertSee('€ 62,50')
            ->assertSee('€ 53,00');

        $this->get(route('tarieven'))
            ->assertRedirect('/vloeistoffen');
    }

    public function test_admin_can_update_shipping_rates(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Brandstoffen', 'slug' => 'brandstoffen']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'GTL webshopproduct',
            'slug' => 'gtl-webshopproduct',
            'price' => 70,
            'active' => true,
        ]);
        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'price' => $product->price,
                'category_id' => $category->id,
                'active' => '1',
                'tier_pricing_enabled' => '1',
                'delivery_tiers' => [
                    ['quantity' => 3, 'price' => 64.95],
                    ['quantity' => 5, 'price' => 60],
                ],
                'pickup_tiers' => [
                    ['quantity' => 3, 'price' => 57.50],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertSee('Voordeel bij meerdere stuks')
            ->assertSee('€ 64,95');

        $priced = app(\App\Services\CartPricing::class)->calculate([
            $product->id => ['quantity' => 3],
        ], 'delivery');
        $this->assertSame(64.95, $priced[$product->id]['price']);
    }

    public function test_old_admin_shipping_rules_url_redirects_to_products(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Brandstoffen', 'slug' => 'brandstoffen']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Selecteerbaar product',
            'slug' => 'selecteerbaar-product',
            'price' => 50,
            'active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.shipping-rules.edit'))
            ->assertRedirect(route('admin.products.index'));
    }

    public function test_admin_can_add_multiple_dynamic_product_rules(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Extra', 'slug' => 'extra']);
        $products = collect(['Product één', 'Product twee', 'Product drie'])->map(
            fn (string $name, int $index) => Product::create([
                'category_id' => $category->id,
                'name' => $name,
                'slug' => 'product-'.($index + 1),
                'price' => 50 + $index,
                'active' => true,
            ])
        );

        $this->actingAs($admin);
        $products->each(function (Product $product, int $index) {
            $this->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'price' => $product->price,
                'category_id' => $product->category_id,
                'active' => '1',
                'tier_pricing_enabled' => '1',
                'delivery_tiers' => [['quantity' => 2 + $index, 'price' => 40 + $index]],
                'pickup_tiers' => [['quantity' => 2 + $index, 'price' => 35 + $index]],
            ])->assertRedirect(route('admin.products.index'))
                ->assertSessionHasNoErrors();
        });

        $this->assertCount(3, app(\App\Services\ShippingRates::class)->productRules());
        foreach ($products as $product) {
            $product->refresh();
            $this->get(route('product.show', $product->slug))
                ->assertSee('Voordeel bij meerdere stuks')
                ->assertSee($product->name);
        }
    }
}
