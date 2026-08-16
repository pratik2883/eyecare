<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Inventory;
use App\Models\Promo;
use App\Models\SyncLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Inventory::count();
        $activeBrands = Brand::where('is_active', true)->count();
        $unsyncedTablets = SyncLog::where('status', '!=', 'success')->count();
        $pendingChanges = Inventory::where('updated_at', '>', now()->subDay())->count();

        $recentSyncs = SyncLog::latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'activeBrands', 'unsyncedTablets', 'pendingChanges', 'recentSyncs'
        ));
    }
}
