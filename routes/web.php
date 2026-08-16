<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Models\Banner;
use App\Models\Promo;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();
    $activeOffers = Promo::where('is_active', true)->orderBy('sort_order')->get();
    return view('home', compact('banners', 'activeOffers'));
});

Route::get('/manifest.json', function () {
    $name = setting('app_name', 'EyeCare Studio');

    return response()->json([
        'name' => $name,
        'short_name' => mb_substr($name, 0, 12),
        'description' => 'Premium eyewear store - discover luxury frames',
        'id' => '/',
        'start_url' => '/',
        'scope' => '/',
        'display' => 'standalone',
        'background_color' => '#1A1A1A',
        'theme_color' => '#1A1A1A',
        'lang' => 'en',
        'categories' => ['shopping', 'lifestyle'],
        'icons' => [
            ['src' => '/images/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => '/images/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'maskable'],
            ['src' => '/images/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => '/images/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
        ],
    ])->header('Content-Type', 'application/manifest+json')
        ->header('Cache-Control', 'no-cache, must-revalidate');
});

Route::get('/category/{category}', [CategoryController::class, 'show'])
    ->where('category', '[A-Za-z0-9_-]+');

Route::get('/product/{inventory:slug}', [ProductController::class, 'show']);
