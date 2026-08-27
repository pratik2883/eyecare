<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function delta(Request $request): JsonResponse
    {
        $lastSynced = $request->query('last_synced');

        $query = Inventory::select([
            'id', 'slug', 'brand_id', 'model_number', 'bq_number', 'name',
            'description', 'about_brand', 'category', 'gender',
            'frame_shape', 'frame_material', 'frame_color', 'frame_size',
            'lens_type', 'price', 'sale_price', 'currency',
            'image_url', 'additional_images', 'stock_quantity',
            'is_active', 'is_new_arrival', 'is_on_sale', 'last_synced_at',
            'created_at', 'updated_at',
        ])->with('brand:id,name,slug')->where('is_active', true);

        if ($lastSynced) {
            $query->where('updated_at', '>', $lastSynced);
        }

        $perPage = min((int) $request->query('per_page', 200), 1000);
        $products = $query->latest('updated_at')->paginate($perPage);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ])->setCache(['private' => true, 'max_age' => 5]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Inventory::select([
            'id', 'slug', 'brand_id', 'model_number', 'bq_number', 'name',
            'category', 'gender', 'frame_shape', 'frame_material',
            'frame_color', 'frame_size', 'price', 'sale_price',
            'image_url', 'is_new_arrival', 'is_on_sale',
        ])->with('brand:id,name,slug,logo_url')->where('is_active', true);

        $this->applyFilters($query, $request);

        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortField, $sortDir);

        $products = $query->paginate($request->get('per_page', 24));

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'filters' => $this->getFilterOptions($request),
            ],
        ]);
    }

    public function show(Request $request, int $inventory): JsonResponse
    {
        $product = Inventory::with('brand:id,name,slug,logo_url')->find($inventory);

        if (!$product) {
            return response()->json(['message' => 'Not found', 'error' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product]);
    }

    public function bulkImport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imported = 0;
        $errors = [];

        try {
            $rows = Excel::toArray([], $request->file('file'));
            $sheet = $rows[0] ?? [];
            $header = array_shift($sheet);

            $mapField = function ($row, $header, $field) {
                $idx = array_search($field, $header);
                return $idx !== false ? ($row[$idx] ?? null) : null;
            };

            foreach ($sheet as $index => $row) {
                try {
                    $brandName = $mapField($row, $header, 'brand') ?? '';
                    $brand = Brand::firstOrCreate(
                        ['slug' => \Str::slug($brandName)],
                        ['name' => $brandName]
                    );

                    $gallery = array_values(array_filter(array_map('trim', explode('|', (string) ($mapField($row, $header, 'gallery_images') ?? '')))));

                    Inventory::updateOrCreate(
                        ['model_number' => $mapField($row, $header, 'model_number') ?? ''],
                        [
                            'brand_id' => $brand->id,
                            'bq_number' => $mapField($row, $header, 'bq_number') ?? $mapField($row, $header, 'BQ NUMBER') ?? null,
                            'name' => $mapField($row, $header, 'name') ?? null,
                            'description' => $mapField($row, $header, 'description') ?? null,
                            'about_brand' => $mapField($row, $header, 'about_brand') ?? $mapField($row, $header, 'ABOUT BRAND') ?? null,
                            'category' => $mapField($row, $header, 'category') ?? 'eyeglasses',
                            'gender' => $mapField($row, $header, 'gender') ?? $mapField($row, $header, 'GENDER') ?? null,
                            'frame_shape' => $mapField($row, $header, 'frame_shape') ?? $mapField($row, $header, 'SHAPE') ?? $mapField($row, $header, 'shape') ?? null,
                            'frame_material' => $mapField($row, $header, 'frame_material') ?? $mapField($row, $header, 'MATERIAL') ?? $mapField($row, $header, 'material') ?? null,
                            'frame_color' => $mapField($row, $header, 'frame_color') ?? $mapField($row, $header, 'COLOUR') ?? $mapField($row, $header, 'color') ?? $mapField($row, $header, 'COLOR') ?? null,
                            'frame_size' => $mapField($row, $header, 'frame_size') ?? $mapField($row, $header, 'SIZE') ?? $mapField($row, $header, 'size') ?? null,
                            'price' => $mapField($row, $header, 'price') ?? 0,
                            'sale_price' => $mapField($row, $header, 'sale_price') ?? null,
                            'image_url' => $mapField($row, $header, 'image_url') ?? null,
                            'additional_images' => $gallery ?: null,
                            'stock_quantity' => $mapField($row, $header, 'stock_quantity') ?? 0,
                            'last_synced_at' => now(),
                        ]
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process file: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message' => "Imported {$imported} products successfully.",
            'imported_count' => $imported,
            'errors' => $errors,
        ]);
    }

    private function applyFilters($query, Request $request, array $skip = []): void
    {
        if ($request->filled('search') && !in_array('search', $skip, true)) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
                    $q->whereRaw('MATCH(model_number, bq_number) AGAINST(? IN BOOLEAN MODE)', [$s]);
                }
                $q->orWhere('model_number', 'like', "%{$s}%")
                  ->orWhere('bq_number', 'like', "%{$s}%")
                  ->orWhereHas('brand', function ($b) use ($s) {
                      $b->where('name', 'like', "%{$s}%")
                        ->orWhere('slug', 'like', "%{$s}%");
                  });
            });
        }
        if ($request->filled('category') && !in_array('category', $skip, true)) {
            $query->where('category', $request->category);
        }
        if ($request->filled('brand_id') && !in_array('brand_id', $skip, true)) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('gender') && !in_array('gender', $skip, true)) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('frame_shape') && !in_array('frame_shape', $skip, true)) {
            $query->where('frame_shape', $request->frame_shape);
        }
        if ($request->filled('frame_material') && !in_array('frame_material', $skip, true)) {
            $query->where('frame_material', $request->frame_material);
        }
        if ($request->filled('frame_color') && !in_array('frame_color', $skip, true)) {
            $query->where('frame_color', $request->frame_color);
        }
        if ($request->filled('frame_size') && !in_array('frame_size', $skip, true)) {
            $query->where('frame_size', $request->frame_size);
        }
        if ($request->filled('min_price') && !in_array('min_price', $skip, true)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price') && !in_array('max_price', $skip, true)) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->boolean('is_new_arrival') && !in_array('is_new_arrival', $skip, true)) {
            $query->where('is_new_arrival', true);
        }
        if ($request->boolean('is_on_sale') && !in_array('is_on_sale', $skip, true)) {
            $query->where('is_on_sale', true);
        }
    }

    private function getFilterOptions(Request $request): array
    {
        $facetValues = function (string $column, array $skip = []) use ($request): array {
            $query = Inventory::where('is_active', true)
                ->select($column)
                ->whereNotNull($column);

            $this->applyFilters($query, $request, $skip);

            return $query->distinct()->orderBy($column)->pluck($column)->filter()->values()->all();
        };

        $brandQuery = Inventory::where('is_active', true)->select('brand_id')->whereNotNull('brand_id');
        $this->applyFilters($brandQuery, $request, ['brand_id']);
        $brandIds = $brandQuery->distinct()->pluck('brand_id')->filter()->values()->all();

        $brands = [];
        if ($brandIds) {
            $brands = Brand::whereIn('id', $brandIds)
                ->where('is_active', true)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->all();
        }

        $priceQuery = Inventory::where('is_active', true);
        $this->applyFilters($priceQuery, $request, ['min_price', 'max_price']);

        return [
            'brands' => $brands,
            'genders' => $facetValues('gender', ['gender']),
            'frame_shapes' => $facetValues('frame_shape', ['frame_shape']),
            'frame_materials' => $facetValues('frame_material', ['frame_material']),
            'frame_colors' => $facetValues('frame_color', ['frame_color']),
            'frame_sizes' => $facetValues('frame_size', ['frame_size']),
            'categories' => $facetValues('category', ['category']),
            'price_range' => [
                'min' => (float) ($priceQuery->min('price') ?? 0),
                'max' => (float) ($priceQuery->max('price') ?? 0),
            ],
        ];
    }
}
