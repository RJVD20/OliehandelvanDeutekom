<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_log(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        AuditLog::query()->create([
            'user_id' => $admin->id,
            'action' => 'updated',
            'subject_type' => 'cms',
            'subject_label' => 'CMS-teksten en homepage',
            'changes' => [
                'home_hero_title' => ['old' => 'Oud', 'new' => 'Nieuw'],
            ],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.audit.index'))
            ->assertOk()
            ->assertSee('Auditlog')
            ->assertSee('CMS-teksten en homepage')
            ->assertSee('home_hero_title');
    }

    public function test_product_status_change_is_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Kachels',
            'slug' => 'kachels',
            'type' => 'kachel',
        ]);
        $product = Product::create([
            'name' => 'Testkachel',
            'slug' => 'testkachel',
            'price' => 100,
            'category_id' => $category->id,
            'active' => true,
        ]);

        $this->actingAs($admin)
            ->patchJson(route('admin.products.toggle-active', $product))
            ->assertOk()
            ->assertJson(['active' => false]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'subject_type' => 'product',
            'subject_id' => $product->id,
            'subject_label' => 'Testkachel',
        ]);

        $log = AuditLog::query()->latest('id')->firstOrFail();
        $this->assertSame(true, $log->changes['active']['old']);
        $this->assertSame(false, $log->changes['active']['new']);
    }
}
