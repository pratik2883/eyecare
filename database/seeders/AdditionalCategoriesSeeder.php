<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdditionalCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $currency = 'INR';

        // 1. Backfill frame_size on any product that is missing it.
        $sizes = ['50-17-140', '52-18-140', '54-18-144', '49-16-138', '51-18-142', '55-18-145', '56-18-145', '48-17-140'];
        $i = 0;
        Inventory::whereNull('frame_size')->orWhere('frame_size', '')
            ->get()
            ->each(function ($p) use ($sizes, &$i) {
                $p->update(['frame_size' => $sizes[$i % count($sizes)]]);
                $i++;
            });

        // 2. Add sample products for the currently-empty categories.
        //    'brand' must be an existing brand name (auto-creates if missing).
        $seed = [
            // KIDS
            ['brand' => 'RAYBAN', 'model_number' => 'RB-K1', 'name' => 'Ray-Ban Junior Round', 'category' => 'kids', 'gender' => 'unisex', 'shape' => 'round', 'material' => 'acetate', 'color' => 'Blue', 'size' => '46-15-125', 'price' => 5500, 'new' => true],
            ['brand' => 'RAYBAN', 'model_number' => 'RB-K2', 'name' => 'Ray-Ban Junior Wayfarer', 'category' => 'kids', 'gender' => 'unisex', 'shape' => 'wayfarer', 'material' => 'acetate', 'color' => 'Black', 'size' => '44-16-130', 'price' => 6000, 'sale' => 4800],
            ['brand' => 'TOM FORD', 'model_number' => 'TF-K1', 'name' => 'Tom Ford Kids Aviator', 'category' => 'kids', 'gender' => 'male', 'shape' => 'aviator', 'material' => 'metal', 'color' => 'Silver', 'size' => '42-14-125', 'price' => 9500, 'new' => true],
            ['brand' => 'GUCCI', 'model_number' => 'GC-K1', 'name' => 'Gucci Kids Cat Eye', 'category' => 'kids', 'gender' => 'female', 'shape' => 'cat-eye', 'material' => 'acetate', 'color' => 'Pink', 'size' => '40-15-120', 'price' => 8800, 'sale' => 7400],
            ['brand' => 'CARTIER', 'model_number' => 'CT-K1', 'name' => 'Cartier Kids Round', 'category' => 'kids', 'gender' => 'unisex', 'shape' => 'round', 'material' => 'titanium', 'color' => 'Gold', 'size' => '41-16-128', 'price' => 12000, 'new' => true],
            ['brand' => 'D&G', 'model_number' => 'DG-K1', 'name' => 'D&G Kids Square', 'category' => 'kids', 'gender' => 'unisex', 'shape' => 'square', 'material' => 'acetate', 'color' => 'Tortoise', 'size' => '43-15-130', 'price' => 7500],
            // ACCESSORIES
            ['brand' => 'PRADA', 'model_number' => 'PR-A1', 'name' => 'Prada Eyewear Case', 'category' => 'accessories', 'gender' => 'unisex', 'shape' => 'cases', 'material' => 'leather', 'color' => 'Black', 'price' => 4500, 'new' => true],
            ['brand' => 'GUCCI', 'model_number' => 'GC-A1', 'name' => 'Gucci Cleaning Kit', 'category' => 'accessories', 'gender' => 'unisex', 'shape' => 'kit', 'material' => 'textile', 'color' => 'Green', 'price' => 1800],
            ['brand' => 'CARTIER', 'model_number' => 'CT-A1', 'name' => 'Cartier Microfibre Cloth', 'category' => 'accessories', 'gender' => 'unisex', 'shape' => 'cloth', 'material' => 'microfibre', 'color' => 'Red', 'price' => 1200, 'sale' => 900],
            // CONTACT LENSES
            ['brand' => 'RAYBAN', 'model_number' => 'RB-C1', 'name' => 'Ray-Ban Vision — Daily', 'category' => 'contact_lenses', 'gender' => 'unisex', 'price' => 1200, 'new' => true],
            ['brand' => 'D&G', 'model_number' => 'DG-C1', 'name' => 'D&G Monthly Lenses', 'category' => 'contact_lenses', 'gender' => 'unisex', 'color' => 'Blue', 'price' => 1400, 'sale' => 1100],
            ['brand' => 'PRADA', 'model_number' => 'PR-C1', 'name' => 'Prada Toric Lenses', 'category' => 'contact_lenses', 'gender' => 'unisex', 'price' => 2600, 'new' => true],
        ];

        $count = 0;
        $brandSlugMap = ['D&G' => 'dolce-gabbana'];
        foreach ($seed as $r) {
            $slug = $brandSlugMap[$r['brand']] ?? Str::slug($r['brand']);
            $brand = Brand::firstOrCreate(
                ['slug' => $slug],
                ['name' => $r['brand']]
            );
            $created = Inventory::updateOrCreate(
                ['model_number' => $r['model_number']],
                [
                    'brand_id' => $brand->id,
                    'name' => $r['name'] ?? null,
                    'description' => null,
                    'about_brand' => null,
                    'category' => $r['category'],
                    'gender' => $r['gender'] ?? 'unisex',
                    'frame_shape' => $r['shape'] ?? null,
                    'frame_material' => $r['material'] ?? null,
                    'frame_color' => $r['color'] ?? null,
                    'frame_size' => $r['size'] ?? null,
                    'price' => $r['price'],
                    'sale_price' => $r['sale'] ?? null,
                    'currency' => $currency,
                    'image_url' => null,
                    'additional_images' => null,
                    'stock_quantity' => $r['stock'] ?? random_int(5, 20),
                    'is_active' => true,
                    'is_new_arrival' => $r['new'] ?? false,
                    'is_on_sale' => isset($r['sale']),
                    'last_synced_at' => $now,
                ]
            );
            if ($created->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->command?->info("Additional categories seeded: {$count} new products. Frame sizes backfilled.");
    }
}