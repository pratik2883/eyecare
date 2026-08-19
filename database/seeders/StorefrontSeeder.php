<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class StorefrontSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Eyeglasses', 'slug' => 'eyeglasses', 'icon' => 'glasses', 'description' => 'Prescription frames for every look', 'sort_order' => 1],
            ['name' => 'Contact Lenses', 'slug' => 'contact_lenses', 'icon' => 'eye', 'description' => 'Crystal-clear vision, zero-frame comfort', 'sort_order' => 2],
            ['name' => 'Sunglasses', 'slug' => 'sunglasses', 'icon' => 'sun', 'description' => 'Designer shades to protect in style', 'sort_order' => 3],
            ['name' => 'Accessories', 'slug' => 'accessories', 'icon' => 'clock', 'description' => 'Cases, cloths and care essentials', 'sort_order' => 4],
            ['name' => 'Kids', 'slug' => 'kids', 'icon' => 'child', 'description' => 'Durable frames made for little ones', 'sort_order' => 5],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], $c);
        }

        $menuItems = [
            ['label' => 'NEW ARRIVALS', 'type' => 'collection', 'ref' => '{"is_new_arrival":1}', 'sort_order' => 1],
            ['label' => 'SUNGLASSES', 'type' => 'category', 'ref' => 'sunglasses', 'sort_order' => 2],
            ['label' => 'EYEGLASSES', 'type' => 'category', 'ref' => 'eyeglasses', 'sort_order' => 3],
            ['label' => 'SHOP BY BRAND', 'type' => 'brands', 'ref' => null, 'sort_order' => 4],
            ['label' => 'KIDS', 'type' => 'category', 'ref' => 'kids', 'sort_order' => 5],
            ['label' => 'SALE', 'type' => 'collection', 'ref' => '{"is_on_sale":1}', 'sort_order' => 6],
            ['label' => 'CONTACT LENSES', 'type' => 'category', 'ref' => 'contact_lenses', 'sort_order' => 7],
            ['label' => 'ACCESSORIES', 'type' => 'category', 'ref' => 'accessories', 'sort_order' => 8],
            ['label' => 'CUSTOMER REVIEWS', 'type' => 'custom', 'link_url' => '#', 'sort_order' => 9],
        ];

        foreach ($menuItems as $i) {
            MenuItem::updateOrCreate(['label' => $i['label'], 'type' => $i['type']], $i);
        }

        $defaults = [
            'store_name' => 'EyeCare Studio',
            'store_tagline' => 'Est. 1969',
            'app_name' => 'EyeCare Studio',
            'section_categories_title' => 'Categories',
            'section_offers_title' => 'Offers & Highlights',
            'section_collection_title' => 'Our Collection',
            'social_facebook' => '',
            'social_instagram' => '',
            'social_youtube' => '',
            'social_linkedin' => '',
            'social_whatsapp' => '',
            'social_twitter' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}