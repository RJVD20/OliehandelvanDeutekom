<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_structured_product_content(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Kachels',
            'slug' => 'kachels',
            'type' => 'kachel',
        ]);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Zibro laserkachel LC 150',
            'price' => 499,
            'category_id' => $category->id,
            'active' => '1',
            'short_description' => 'Krachtige laserkachel voor grote ruimtes.',
            'description' => 'Aanvullende uitleg over het zuinige en veilige gebruik.',
            'specifications' => [
                ['name' => 'Vermogen', 'value' => '4800 W'],
                ['name' => 'Tankinhoud', 'value' => '7,6 liter'],
            ],
        ])->assertRedirect(route('admin.products.index'))
            ->assertSessionHasNoErrors();

        $product = Product::firstOrFail();

        $this->assertSame('Krachtige laserkachel voor grote ruimtes.', $product->short_description);
        $this->assertCount(2, $product->specifications);

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertSee('Krachtige laserkachel voor grote ruimtes.')
            ->assertSee('Aanvullende uitleg over het zuinige en veilige gebruik.')
            ->assertSee('Vermogen')
            ->assertSee('4800 W')
            ->assertSee('Tankinhoud')
            ->assertSee('7,6 liter');
    }
}
