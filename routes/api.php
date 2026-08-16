<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\InventoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/inventory/delta', [InventoryController::class, 'delta']);
    Route::post('/inventory/bulk-import', [InventoryController::class, 'bulkImport']);
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/{inventory}', [InventoryController::class, 'show']);

    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/top', [BrandController::class, 'topBrands']);
});
