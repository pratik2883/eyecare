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
                ->selectRaw('category, group_concat(distinct frame_shape order by frame_shape separator "|") as shapes')
                ->groupBy('category')
                ->pluck('shapes', 'category');

            $view->with('menuItems', $items)->with('menuShapesByCategory', $shapesByCat);
        });
    }
}
