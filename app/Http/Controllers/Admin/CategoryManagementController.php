<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryManagementController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->orderBy('id')->get();
        $counts = Inventory::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('admin.categories.index', compact('categories', 'counts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($request->filled('slug') ? $request->slug : $data['name']);

        if (Category::where('slug', $slug)->exists()) {
            return back()->withInput()->withErrors(['slug' => "Slug '{$slug}' is already in use."]);
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? Category::max('sort_order') + 1;

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($request->filled('slug') ? $request->slug : $data['name']);
        if (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            return back()->withInput()->withErrors(['slug' => "Slug '{$slug}' is already in use."]);
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if (Inventory::where('category', $category->slug)->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Category '{$category->name}' still has products. Deactivate it instead of deleting.");
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'direction' => 'required|in:up,down']);
        $list = Category::orderBy('sort_order')->orderBy('id')->get();
        $idx = $list->search(fn($c) => (int) $c->id === (int) $request->id);
        $swap = $request->direction === 'up' ? $idx - 1 : $idx + 1;
        if ($idx !== false && isset($list[$swap])) {
            $a = $list[$idx];
            $b = $list[$swap];
            $tmp = $a->sort_order;
            $a->update(['sort_order' => $b->sort_order]);
            $b->update(['sort_order' => $tmp]);
        }
        return back()->with('success', 'Categories reordered.');
    }
}