<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\MenuItem;
use App\Models\Promo;
use App\Observers\InventoryObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFour();

        Inventory::observe(InventoryObserver::class);

        View::composer('*', function ($view) {
            $view->with('activeOffers', Promo::where('is_active', true)->orderBy('sort_order')->take(2)->get());
        });

        View::composer('home', function ($view) {
            $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
            $counts = Inventory::where('is_active', true)
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category');

            $view->with('categories', $categories)->with('categoryCounts', $counts);
        });

        View::composer('partials.side-drawer', function ($view) {
            $items = MenuItem::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
            $shapesByCat = Inventory::where('is_active', true)
                ->whereNotNull('frame_shape')
                ->where('frame_shape', '!=', '')
                ->select('category', 'frame_shape')
                ->distinct()
                ->orderBy('frame_shape')
                ->get()
                ->groupBy('category')
                ->map(fn ($group) => $group->pluck('frame_shape')->implode('|'));

            $view->with('menuItems', $items)->with('menuShapesByCategory', $shapesByCat);
        });
    }
}
