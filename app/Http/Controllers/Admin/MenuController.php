<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $items = MenuItem::orderBy('sort_order')->orderBy('id')->get();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.menu.index', compact('items', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:category,brand,brands,collection,custom',
            'ref' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? MenuItem::max('sort_order') + 1;

        MenuItem::create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item added.');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|in:category,brand,brands,collection,custom',
            'ref' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $menuItem->update($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu item removed.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'direction' => 'required|in:up,down']);
        $list = MenuItem::orderBy('sort_order')->orderBy('id')->get();
        $idx = $list->search(fn($m) => (int) $m->id === (int) $request->id);
        $swap = $request->direction === 'up' ? $idx - 1 : $idx + 1;
        if ($idx !== false && isset($list[$swap])) {
            $a = $list[$idx];
            $b = $list[$swap];
            $tmp = $a->sort_order;
            $a->update(['sort_order' => $b->sort_order]);
            $b->update(['sort_order' => $tmp]);
        }
        return back()->with('success', 'Menu reordered.');
    }
}