<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\Shape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('brand');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('model_number', 'like', "%{$s}%")
                  ->orWhere('bq_number', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhereHas('brand', function ($b) use ($s) {
                      $b->where('name', 'like', "%{$s}%")
                        ->orWhere('slug', 'like', "%{$s}%");
                  });
            });
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('frame_shape')) {
            $query->where('frame_shape', $request->frame_shape);
        }
        if ($request->filled('frame_material')) {
            $query->where('frame_material', $request->frame_material);
        }
        if ($request->filled('frame_color')) {
            $query->where('frame_color', $request->frame_color);
        }
        if ($request->filled('frame_size')) {
            $query->where('frame_size', $request->frame_size);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }
        if ($request->boolean('is_new_arrival')) {
            $query->where('is_new_arrival', true);
        }
        if ($request->boolean('is_on_sale')) {
            $query->where('is_on_sale', true);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $brands = Brand::orderBy('name')->get();
        $categories = Inventory::distinct()->orderBy('category')->pluck('category')->filter()->values();
        $genders = Inventory::distinct()->orderBy('gender')->pluck('gender')->filter()->values();
        $shapes = Inventory::distinct()->orderBy('frame_shape')->pluck('frame_shape')->filter()->values();
        $materials = Inventory::distinct()->orderBy('frame_material')->pluck('frame_material')->filter()->values();
        $colors = Inventory::distinct()->orderBy('frame_color')->pluck('frame_color')->filter()->values();
        $sizes = Inventory::distinct()->orderBy('frame_size')->pluck('frame_size')->filter()->values();

        return view('admin.inventory.index', compact(
            'products', 'brands', 'categories', 'genders', 'shapes', 'materials', 'colors', 'sizes'
        ));
    }

    public function create()
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $shapes = Shape::where('is_active', true)->orderBy('name')->get();
        $colors = Color::where('is_active', true)->orderBy('name')->get();
        $categories = ['eyeglasses', 'sunglasses', 'contact_lenses', 'accessories', 'kids'];

        return view('admin.inventory.form', compact('brands', 'materials', 'shapes', 'colors', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model_number' => 'required|string|max:100',
            'bq_number' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'about_brand' => 'nullable|string',
            'category' => 'required|string',
            'gender' => 'nullable|string',
            'frame_shape' => 'nullable|string|max:100',
            'frame_material' => 'nullable|string|max:100',
            'frame_color' => 'nullable|string|max:100',
            'frame_size' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:price',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'nullable|string|max:500',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'stock_quantity' => 'integer|min:0',
            'is_active' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_on_sale' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_new_arrival'] = $request->boolean('is_new_arrival');
        $data['is_on_sale'] = $request->boolean('is_on_sale');
        $data['currency'] = 'INR';
        $data['stock_quantity'] = $request->filled('stock_quantity') ? (int) $request->stock_quantity : 1;

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::url($request->file('image')->store('products', 'public'));
        }

        if ($request->hasFile('gallery_images')) {
            $data['additional_images'] = collect($request->file('gallery_images'))
                ->map(fn($file) => Storage::url($file->store('products/gallery', 'public')))
                ->values()
                ->all();
        }

        Inventory::create($data);

        Cache::forget('filter_options');

        return redirect()->route('admin.inventory.index')->with('success', 'Product created successfully.');
    }

    public function edit(Inventory $inventory)
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $shapes = Shape::where('is_active', true)->orderBy('name')->get();
        $colors = Color::where('is_active', true)->orderBy('name')->get();
        $categories = ['eyeglasses', 'sunglasses', 'contact_lenses', 'accessories', 'kids'];

        return view('admin.inventory.form', compact('inventory', 'brands', 'materials', 'shapes', 'colors', 'categories'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model_number' => 'required|string|max:100',
            'bq_number' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'about_brand' => 'nullable|string',
            'category' => 'required|string',
            'gender' => 'nullable|string',
            'frame_shape' => 'nullable|string|max:100',
            'frame_material' => 'nullable|string|max:100',
            'frame_color' => 'nullable|string|max:100',
            'frame_size' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:price',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'nullable|string|max:500',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'stock_quantity' => 'integer|min:0',
            'is_active' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_on_sale' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_new_arrival'] = $request->boolean('is_new_arrival');
        $data['is_on_sale'] = $request->boolean('is_on_sale');
        $data['stock_quantity'] = $request->filled('stock_quantity') ? (int) $request->stock_quantity : $inventory->stock_quantity ?? 1;

        if ($request->hasFile('image')) {
            if ($inventory->image_url && str_contains($inventory->image_url, '/storage/products/')) {
                $oldPath = str_replace('/storage/', '', $inventory->image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['image_url'] = Storage::url($request->file('image')->store('products', 'public'));
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($inventory->additional_images ?? [] as $old) {
                if (str_contains($old, '/storage/products/')) {
                    $oldPath = str_replace('/storage/', '', $old);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
            $data['additional_images'] = collect($request->file('gallery_images'))
                ->map(fn($file) => Storage::url($file->store('products/gallery', 'public')))
                ->values()
                ->all();
        }

        $inventory->update($data);

        Cache::forget('filter_options');

        return redirect()->route('admin.inventory.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        if ($inventory->image_url && str_contains($inventory->image_url, '/storage/products/')) {
            $oldPath = str_replace('/storage/', '', $inventory->image_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        foreach ($inventory->additional_images ?? [] as $old) {
            if (str_contains($old, '/storage/products/')) {
                $oldPath = str_replace('/storage/', '', $old);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $inventory->delete();

        Cache::forget('filter_options');

        return redirect()->route('admin.inventory.index')->with('success', 'Product deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:inventory,id',
        ]);

        $products = Inventory::whereIn('id', $data['ids'])->get();

        foreach ($products as $inventory) {
            if ($inventory->image_url && str_contains($inventory->image_url, '/storage/products/')) {
                $oldPath = str_replace('/storage/', '', $inventory->image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            foreach ($inventory->additional_images ?? [] as $old) {
                if (str_contains($old, '/storage/products/')) {
                    $oldPath = str_replace('/storage/', '', $old);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
        }

        $count = Inventory::whereIn('id', $data['ids'])->delete();

        Cache::forget('filter_options');

        return redirect()->route('admin.inventory.index')->with('success', "{$count} product(s) deleted successfully.");
    }
}
