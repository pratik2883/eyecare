<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class OpticalStoreSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'MAYBACH', 'slug' => 'maybach', 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'TOM FORD', 'slug' => 'tom-ford', 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'MONTBLANC', 'slug' => 'montblanc', 'is_featured' => true, 'sort_order' => 3],
            ['name' => 'GUCCI', 'slug' => 'gucci', 'is_featured' => true, 'sort_order' => 4],
            ['name' => 'BURBERRY', 'slug' => 'burberry', 'is_featured' => true, 'sort_order' => 5],
            ['name' => 'PERSOL', 'slug' => 'persol', 'is_featured' => true, 'sort_order' => 6],
            ['name' => 'PRADA', 'slug' => 'prada', 'is_featured' => true, 'sort_order' => 7],
            ['name' => 'D&G', 'slug' => 'dolce-gabbana', 'is_featured' => true, 'sort_order' => 8],
            ['name' => 'CARTIER', 'slug' => 'cartier', 'is_featured' => true, 'sort_order' => 9],
            ['name' => 'RAYBAN', 'slug' => 'rayban', 'is_featured' => true, 'sort_order' => 10],
        ];

        foreach ($brands as $data) {
            Brand::create($data);
        }

        $brandIds = Brand::pluck('id', 'slug');

        $products = [
            // MAYBACH
            ['brand_slug' => 'maybach', 'model_number' => 'MB-1001', 'name' => 'Maybach Classic', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'rectangular', 'frame_material' => 'titanium', 'frame_color' => 'Gold', 'price' => 45000, 'is_new_arrival' => true, 'is_on_sale' => false],
            ['brand_slug' => 'maybach', 'model_number' => 'MB-1002', 'name' => 'Maybach Aviator', 'category' => 'sunglasses', 'gender' => 'male', 'frame_shape' => 'aviator', 'frame_material' => 'metal', 'frame_color' => 'Silver', 'price' => 55000, 'is_new_arrival' => true],
            // TOM FORD
            ['brand_slug' => 'tom-ford', 'model_number' => 'TF-2001', 'name' => 'Tom Ford Henry', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'square', 'frame_material' => 'acetate', 'frame_color' => 'Black', 'price' => 35000, 'is_on_sale' => true, 'sale_price' => 28000],
            ['brand_slug' => 'tom-ford', 'model_number' => 'TF-2002', 'name' => 'Tom Ford Nicole', 'category' => 'sunglasses', 'gender' => 'female', 'frame_shape' => 'cat-eye', 'frame_material' => 'acetate', 'frame_color' => 'Tortoise', 'price' => 38000, 'is_new_arrival' => true],
            // MONTBLANC
            ['brand_slug' => 'montblanc', 'model_number' => 'MB-3001', 'name' => 'Montblanc Legend', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'rectangular', 'frame_material' => 'titanium', 'frame_color' => 'Gunmetal', 'price' => 42000, 'is_new_arrival' => true],
            ['brand_slug' => 'montblanc', 'model_number' => 'MB-3002', 'name' => 'Montblanc Summit', 'category' => 'sunglasses', 'gender' => 'unisex', 'frame_shape' => 'wayfarer', 'frame_material' => 'metal', 'frame_color' => 'Black', 'price' => 48000],
            // GUCCI
            ['brand_slug' => 'gucci', 'model_number' => 'GC-4001', 'name' => 'Gucci Flora', 'category' => 'eyeglasses', 'gender' => 'female', 'frame_shape' => 'round', 'frame_material' => 'acetate', 'frame_color' => 'Pink Gold', 'price' => 32000, 'is_on_sale' => true, 'sale_price' => 25000],
            ['brand_slug' => 'gucci', 'model_number' => 'GC-4002', 'name' => 'Gucci GG', 'category' => 'sunglasses', 'gender' => 'female', 'frame_shape' => 'cat-eye', 'frame_material' => 'metal', 'frame_color' => 'Gold', 'price' => 36000, 'is_new_arrival' => true],
            // BURBERRY
            ['brand_slug' => 'burberry', 'model_number' => 'BB-5001', 'name' => 'Burberry Classic', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'square', 'frame_material' => 'acetate', 'frame_color' => 'Havana', 'price' => 28000],
            ['brand_slug' => 'burberry', 'model_number' => 'BB-5002', 'name' => 'Burberry Check', 'category' => 'sunglasses', 'gender' => 'unisex', 'frame_shape' => 'wayfarer', 'frame_material' => 'metal', 'frame_color' => 'Brown', 'price' => 31000, 'is_on_sale' => true, 'sale_price' => 24000],
            // PERSOL
            ['brand_slug' => 'persol', 'model_number' => 'PS-6001', 'name' => 'Persol PO3001', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'round', 'frame_material' => 'metal', 'frame_color' => 'Silver', 'price' => 25000, 'is_new_arrival' => true],
            ['brand_slug' => 'persol', 'model_number' => 'PS-6002', 'name' => 'Persol 649', 'category' => 'sunglasses', 'gender' => 'unisex', 'frame_shape' => 'round', 'frame_material' => 'acetate', 'frame_color' => 'Blue', 'price' => 29000],
            // PRADA
            ['brand_slug' => 'prada', 'model_number' => 'PR-7001', 'name' => 'Prada Linea Rossa', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'square', 'frame_material' => 'titanium', 'frame_color' => 'Black', 'price' => 33000, 'is_on_sale' => true, 'sale_price' => 26000],
            ['brand_slug' => 'prada', 'model_number' => 'PR-7002', 'name' => 'Prada Symbole', 'category' => 'sunglasses', 'gender' => 'female', 'frame_shape' => 'cat-eye', 'frame_material' => 'acetate', 'frame_color' => 'White', 'price' => 37000, 'is_new_arrival' => true],
            // D&G
            ['brand_slug' => 'dolce-gabbana', 'model_number' => 'DG-8001', 'name' => 'D&G Devotion', 'category' => 'eyeglasses', 'gender' => 'female', 'frame_shape' => 'round', 'frame_material' => 'metal', 'frame_color' => 'Gold', 'price' => 30000],
            ['brand_slug' => 'dolce-gabbana', 'model_number' => 'DG-8002', 'name' => 'D&G King', 'category' => 'sunglasses', 'gender' => 'male', 'frame_shape' => 'aviator', 'frame_material' => 'metal', 'frame_color' => 'Silver', 'price' => 34000, 'is_on_sale' => true, 'sale_price' => 27000],
            // CARTIER
            ['brand_slug' => 'cartier', 'model_number' => 'CT-9001', 'name' => 'Cartier Santos', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'rectangular', 'frame_material' => 'titanium', 'frame_color' => 'Gold', 'price' => 65000, 'is_new_arrival' => true],
            ['brand_slug' => 'cartier', 'model_number' => 'CT-9002', 'name' => 'Cartier Trinity', 'category' => 'sunglasses', 'gender' => 'female', 'frame_shape' => 'round', 'frame_material' => 'metal', 'frame_color' => 'Rose Gold', 'price' => 72000],
            // RAYBAN
            ['brand_slug' => 'rayban', 'model_number' => 'RB-1001', 'name' => 'Ray-Ban Aviator', 'category' => 'sunglasses', 'gender' => 'unisex', 'frame_shape' => 'aviator', 'frame_material' => 'metal', 'frame_color' => 'Gold', 'price' => 15000, 'is_on_sale' => true, 'sale_price' => 11000],
            ['brand_slug' => 'rayban', 'model_number' => 'RB-1002', 'name' => 'Ray-Ban Wayfarer', 'category' => 'eyeglasses', 'gender' => 'unisex', 'frame_shape' => 'wayfarer', 'frame_material' => 'acetate', 'frame_color' => 'Black', 'price' => 12000, 'is_new_arrival' => true],
            ['brand_slug' => 'rayban', 'model_number' => 'RB-1003', 'name' => 'Ray-Ban Clubmaster', 'category' => 'eyeglasses', 'gender' => 'male', 'frame_shape' => 'square', 'frame_material' => 'acetate', 'frame_color' => 'Tortoise', 'price' => 13500],
            ['brand_slug' => 'rayban', 'model_number' => 'RB-1004', 'name' => 'Ray-Ban Round', 'category' => 'sunglasses', 'gender' => 'female', 'frame_shape' => 'round', 'frame_material' => 'metal', 'frame_color' => 'Silver', 'price' => 14000, 'is_on_sale' => true, 'sale_price' => 10500],
        ];

        foreach ($products as $data) {
            $brandSlug = $data['brand_slug'];
            unset($data['brand_slug']);
            $data['brand_id'] = $brandIds[$brandSlug] ?? 1;
            $data['stock_quantity'] = rand(5, 25);
            $data['currency'] = 'INR';
            $data['last_synced_at'] = now();
            Inventory::create($data);
        }
    }
}
