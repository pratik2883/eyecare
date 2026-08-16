<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\ColorMaterialMapping;
use App\Models\Material;
use App\Models\Shape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    // ─── BRANDS ───
    public function brands()
    {
        $brands = Brand::orderBy('name')->get();
        return view('admin.attributes.brands', compact('brands'));
    }

    public function brandsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'logo_url' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('logo')) {
            $data['logo_url'] = Storage::url($request->file('logo')->store('brands', 'public'));
        }

        Brand::create($data);
        return redirect()->route('admin.attributes.brands')->with('success', 'Brand created.');
    }

    public function brandsUpdate(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'logo_url' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('logo')) {
            if ($brand->logo_url && str_contains($brand->logo_url, '/storage/brands/')) {
                $oldPath = str_replace('/storage/', '', $brand->logo_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['logo_url'] = Storage::url($request->file('logo')->store('brands', 'public'));
        }

        $brand->update($data);
        return redirect()->route('admin.attributes.brands')->with('success', 'Brand updated.');
    }

    public function brandsDestroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return back()->with('error', "Cannot delete brand '{$brand->name}' — it has products. Reassign or delete them first.");
        }

        $brand->delete();
        return redirect()->route('admin.attributes.brands')->with('success', 'Brand deleted.');
    }

    // ─── MATERIALS ───
    public function materials()
    {
        $materials = Material::orderBy('name')->get();
        return view('admin.attributes.materials', compact('materials'));
    }

    public function materialsStore(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['slug'] = Str::slug($request->name);
        Material::create($data);
        return redirect()->route('admin.attributes.materials')->with('success', 'Material created.');
    }

    public function materialsUpdate(Request $request, Material $material)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['slug'] = Str::slug($request->name);
        $material->update($data);
        return redirect()->route('admin.attributes.materials')->with('success', 'Material updated.');
    }

    public function materialsDestroy(Material $material)
    {
        $material->delete();
        return redirect()->route('admin.attributes.materials')->with('success', 'Material deleted.');
    }

    // ─── SHAPES ───
    public function shapes()
    {
        $shapes = Shape::orderBy('name')->get();
        return view('admin.attributes.shapes', compact('shapes'));
    }

    public function shapesStore(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['slug'] = Str::slug($request->name);
        Shape::create($data);
        return redirect()->route('admin.attributes.shapes')->with('success', 'Shape created.');
    }

    public function shapesUpdate(Request $request, Shape $shape)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['slug'] = Str::slug($request->name);
        $shape->update($data);
        return redirect()->route('admin.attributes.shapes')->with('success', 'Shape updated.');
    }

    public function shapesDestroy(Shape $shape)
    {
        $shape->delete();
        return redirect()->route('admin.attributes.shapes')->with('success', 'Shape deleted.');
    }

    // ─── COLORS ───
    public function colors()
    {
        $colors = Color::orderBy('name')->get();
        return view('admin.attributes.colors', compact('colors'));
    }

    public function colorsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'hex_code' => ['nullable', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);
        $data['slug'] = Str::slug($request->name);
        Color::create($data);
        return redirect()->route('admin.attributes.colors')->with('success', 'Color created.');
    }

    public function colorsUpdate(Request $request, Color $color)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'hex_code' => ['nullable', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);
        $data['slug'] = Str::slug($request->name);
        $color->update($data);
        return redirect()->route('admin.attributes.colors')->with('success', 'Color updated.');
    }

    public function colorsDestroy(Color $color)
    {
        $color->delete();
        return redirect()->route('admin.attributes.colors')->with('success', 'Color deleted.');
    }

    // ─── COLOR / MATERIAL NORMALIZER ───
    public function normalizer()
    {
        $mappings = ColorMaterialMapping::orderBy('type')->orderBy('raw_value')->get();
        $distinctColors = \App\Models\Inventory::whereNotNull('frame_color')
            ->distinct()->orderBy('frame_color')->pluck('frame_color');
        $distinctMaterials = \App\Models\Inventory::whereNotNull('frame_material')
            ->distinct()->orderBy('frame_material')->pluck('frame_material');
        $masterColors = Color::orderBy('name')->pluck('name');
        $masterMaterials = Material::orderBy('name')->pluck('name');

        return view('admin.attributes.normalizer', compact(
            'mappings', 'distinctColors', 'distinctMaterials', 'masterColors', 'masterMaterials'
        ));
    }

    public function normalizerStore(Request $request)
    {
        $data = $request->validate([
            'raw_value' => 'required|string|max:255',
            'type' => 'required|in:color,material',
            'mapped_value' => 'required|string|max:255',
        ]);
        ColorMaterialMapping::updateOrCreate(
            ['raw_value' => $data['raw_value'], 'type' => $data['type']],
            ['mapped_value' => $data['mapped_value']]
        );
        return redirect()->route('admin.attributes.normalizer')->with('success', 'Mapping saved.');
    }

    public function normalizerDestroy(ColorMaterialMapping $mapping)
    {
        $mapping->delete();
        return redirect()->route('admin.attributes.normalizer')->with('success', 'Mapping deleted.');
    }

    public function normalizerApply()
    {
        $updated = 0;

        DB::transaction(function () use (&$updated) {
            $colorMappings = ColorMaterialMapping::where('type', 'color')->get();
            foreach ($colorMappings as $m) {
                $count = Inventory::where('frame_color', $m->raw_value)
                    ->update(['frame_color' => $m->mapped_value]);
                $updated += $count;
            }

            $materialMappings = ColorMaterialMapping::where('type', 'material')->get();
            foreach ($materialMappings as $m) {
                $count = Inventory::where('frame_material', $m->raw_value)
                    ->update(['frame_material' => $m->mapped_value]);
                $updated += $count;
            }
        });

        Cache::forget('filter_options');

        return redirect()->route('admin.attributes.normalizer')->with('success', "Applied mappings to {$updated} products.");
    }
}
