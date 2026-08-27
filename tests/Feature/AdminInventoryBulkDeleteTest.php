<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInventoryBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_bulk_delete_inventory(): void
    {
        $response = $this->post(route('admin.inventory.bulk-destroy'), [
            'ids' => [1, 2],
        ]);

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_bulk_delete_selected_products(): void
    {
        $user = User::factory()->create();
        $brand = Brand::create(['name' => 'Ray-Ban', 'slug' => 'ray-ban', 'is_active' => true]);

        $product1 = Inventory::create([
            'brand_id' => $brand->id,
            'model_number' => 'RB3025-01',
            'category' => 'sunglasses',
            'price' => 5000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $product2 = Inventory::create([
            'brand_id' => $brand->id,
            'model_number' => 'RB3025-02',
            'category' => 'sunglasses',
            'price' => 6000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $product3 = Inventory::create([
            'brand_id' => $brand->id,
            'model_number' => 'RB3025-03',
            'category' => 'sunglasses',
            'price' => 7000,
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.inventory.bulk-destroy'), [
            'ids' => [$product1->id, $product2->id],
        ]);

        $response->assertRedirect(route('admin.inventory.index'));
        $response->assertSessionHas('success', '2 product(s) deleted successfully.');

        $this->assertDatabaseMissing('inventory', ['id' => $product1->id]);
        $this->assertDatabaseMissing('inventory', ['id' => $product2->id]);
        $this->assertDatabaseHas('inventory', ['id' => $product3->id]);
    }

    public function test_bulk_delete_requires_ids_array(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.inventory.bulk-destroy'), [
            'ids' => [],
        ]);

        $response->assertSessionHasErrors(['ids']);
    }
}
