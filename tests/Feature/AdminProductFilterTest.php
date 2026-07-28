<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_name_with_apostrophe_is_filled_in_on_edit_form(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Kachels',
            'slug' => 'kachels',
            'type' => 'kachel',
        ]);
        $product = Product::create([
            'name' => "Qlima's laserkachel",
            'slug' => 'qlimas-laserkachel',
            'price' => 100,
            'category_id' => $category->id,
            'active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('value="Qlima&#039;s laserkachel"', false)
            ->assertSee('value="qlimas-laserkachel"', false);
    }

    public function test_admin_can_filter_products_by_active_brand(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Kachels',
            'slug' => 'kachels',
            'type' => 'kachel',
        ]);

        Product::create([
            'name' => 'Actieve Zibro',
            'slug' => 'actieve-zibro',
            'price' => 100,
            'category_id' => $category->id,
            'brand' => 'Zibro',
            'active' => true,
        ]);
        Product::create([
            'name' => 'Inactieve Zibro',
            'slug' => 'inactieve-zibro',
            'price' => 90,
            'category_id' => $category->id,
            'brand' => 'Zibro',
            'active' => false,
        ]);
        Product::create([
            'name' => 'Inactieve Toyotomi',
            'slug' => 'inactieve-toyotomi',
            'price' => 80,
            'category_id' => $category->id,
            'brand' => 'Toyotomi',
            'active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('<option value="Zibro"', false)
            ->assertDontSee('<option value="Toyotomi"', false);

        $this->actingAs($admin)
            ->get(route('admin.products.index', ['brand' => 'Zibro']))
            ->assertOk()
            ->assertSee('Actieve Zibro')
            ->assertSee('Inactieve Zibro')
            ->assertDontSee('Inactieve Toyotomi');
    }
}
