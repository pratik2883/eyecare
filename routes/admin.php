<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BulkImportController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('web')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Banners
        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::post('/banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
        Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

        // Promos
        Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
        Route::post('/promos', [PromoController::class, 'store'])->name('promos.store');
        Route::put('/promos/{promo}', [PromoController::class, 'update'])->name('promos.update');
        Route::delete('/promos/{promo}', [PromoController::class, 'destroy'])->name('promos.destroy');

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::post('/inventory/bulk-destroy', [InventoryController::class, 'bulkDestroy'])->name('inventory.bulk-destroy');
        Route::get('/inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

        // Bulk Import
        Route::get('/bulk-import', [BulkImportController::class, 'index'])->name('bulk-import.index');
        Route::post('/bulk-import/preview', [BulkImportController::class, 'preview'])->name('bulk-import.preview');
        Route::post('/bulk-import/import', [BulkImportController::class, 'import'])->name('bulk-import.import');

        // Attributes
        Route::get('/attributes/brands', [AttributeController::class, 'brands'])->name('attributes.brands');
        Route::post('/attributes/brands', [AttributeController::class, 'brandsStore'])->name('attributes.brands.store');
        Route::put('/attributes/brands/{brand}', [AttributeController::class, 'brandsUpdate'])->name('attributes.brands.update');
        Route::delete('/attributes/brands/{brand}', [AttributeController::class, 'brandsDestroy'])->name('attributes.brands.destroy');

        Route::get('/attributes/materials', [AttributeController::class, 'materials'])->name('attributes.materials');
        Route::post('/attributes/materials', [AttributeController::class, 'materialsStore'])->name('attributes.materials.store');
        Route::put('/attributes/materials/{material}', [AttributeController::class, 'materialsUpdate'])->name('attributes.materials.update');
        Route::delete('/attributes/materials/{material}', [AttributeController::class, 'materialsDestroy'])->name('attributes.materials.destroy');

        Route::get('/attributes/shapes', [AttributeController::class, 'shapes'])->name('attributes.shapes');
        Route::post('/attributes/shapes', [AttributeController::class, 'shapesStore'])->name('attributes.shapes.store');
        Route::put('/attributes/shapes/{shape}', [AttributeController::class, 'shapesUpdate'])->name('attributes.shapes.update');
        Route::delete('/attributes/shapes/{shape}', [AttributeController::class, 'shapesDestroy'])->name('attributes.shapes.destroy');

        Route::get('/attributes/colors', [AttributeController::class, 'colors'])->name('attributes.colors');
        Route::post('/attributes/colors', [AttributeController::class, 'colorsStore'])->name('attributes.colors.store');
        Route::put('/attributes/colors/{color}', [AttributeController::class, 'colorsUpdate'])->name('attributes.colors.update');
        Route::delete('/attributes/colors/{color}', [AttributeController::class, 'colorsDestroy'])->name('attributes.colors.destroy');

        Route::get('/attributes/normalizer', [AttributeController::class, 'normalizer'])->name('attributes.normalizer');
        Route::post('/attributes/normalizer', [AttributeController::class, 'normalizerStore'])->name('attributes.normalizer.store');
        Route::delete('/attributes/normalizer/{mapping}', [AttributeController::class, 'normalizerDestroy'])->name('attributes.normalizer.destroy');
        Route::post('/attributes/normalizer/apply', [AttributeController::class, 'normalizerApply'])->name('attributes.normalizer.apply');

        // Categories
        Route::get('/categories', [CategoryManagementController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryManagementController::class, 'store'])->name('categories.store');
        Route::post('/categories/reorder', [CategoryManagementController::class, 'reorder'])->name('categories.reorder');
        Route::put('/categories/{category}', [CategoryManagementController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryManagementController::class, 'destroy'])->name('categories.destroy');

        // Menu
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::post('/menu/reorder', [MenuController::class, 'reorder'])->name('menu.reorder');
        Route::put('/menu/{menuItem}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{menuItem}', [MenuController::class, 'destroy'])->name('menu.destroy');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Sync
        Route::get('/sync', [SyncController::class, 'index'])->name('sync.index');
        Route::post('/sync/trigger', [SyncController::class, 'triggerSync'])->name('sync.trigger');
    });
});
