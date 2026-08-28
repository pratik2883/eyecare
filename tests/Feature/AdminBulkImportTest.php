<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminBulkImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_preview_csv_file(): void
    {
        $user = User::factory()->create();

        $content = "brand,model_number,price,category\nRay-Ban,RB3025,5000,sunglasses\nOakley,OO9013,6000,sunglasses";
        $file = UploadedFile::fake()->createWithContent('products.csv', $content);

        $response = $this->actingAs($user)->post(route('admin.bulk-import.preview'), [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.bulk-import.preview');
        $response->assertViewHas('headers', ['brand', 'model_number', 'price', 'category']);
    }

    public function test_admin_can_import_products_from_session(): void
    {
        $user = User::factory()->create();

        $sheet = [
            ['Ray-Ban', 'RB3025-TEST', '5500', 'sunglasses'],
        ];

        $importId = 'test_import_123';
        session(['import_' . $importId => $sheet]);

        $response = $this->actingAs($user)->post(route('admin.bulk-import.import'), [
            'import_id' => $importId,
            'mapping' => [
                'brand' => 0,
                'model_number' => 1,
                'price' => 2,
                'category' => 3,
            ],
        ]);

        $response->assertRedirect(route('admin.bulk-import.index'));
        $response->assertSessionHas('success', 'Imported 1 products successfully.');

        $this->assertDatabaseHas('inventory', [
            'model_number' => 'RB3025-TEST',
            'price' => 5500,
        ]);
    }
}
