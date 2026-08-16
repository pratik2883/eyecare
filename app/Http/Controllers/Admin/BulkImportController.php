<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class BulkImportController extends Controller
{
    public function index()
    {
        return view('admin.bulk-import.index');
    }

    public function preview(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);

        $rows = Excel::toArray([], $request->file('file'));
        $sheet = $rows[0] ?? [];

        if (empty($sheet)) {
            return back()->with('error', 'Excel file is empty.');
        }

        $headers = array_shift($sheet);
        $importId = Str::random(16);
        session(['import_' . $importId => $sheet]);

        return view('admin.bulk-import.preview', compact('headers', 'sheet', 'importId'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'mapping' => 'required|array',
            'import_id' => 'required|string',
        ]);

        $sheet = session('import_' . $request->import_id);
        if (!$sheet) {
            return redirect()->route('admin.bulk-import.index')->with('error', 'Import session expired. Please re-upload.');
        }

        $mapping = $request->mapping;
        $imported = 0;
        $errors = [];

        foreach ($sheet as $index => $row) {
            try {
                $data = [];
                foreach ($mapping as $field => $colIndex) {
                    if ($colIndex !== '' && isset($row[$colIndex])) {
                        $data[$field] = $row[$colIndex];
                    }
                }

                if (empty($data['model_number']) || empty($data['price'])) {
                    $errors[] = "Row " . ($index + 2) . ": model_number and price are required.";
                    continue;
                }

                if (!empty($data['brand'])) {
                    $brand = Brand::firstOrCreate(
                        ['slug' => Str::slug($data['brand'])],
                        ['name' => $data['brand']]
                    );
                    $data['brand_id'] = $brand->id;
                    unset($data['brand']);
                }

                $fieldMap = [
                    'color' => 'frame_color',
                    'material' => 'frame_material',
                    'shape' => 'frame_shape',
                    'size' => 'frame_size',
                ];
                foreach ($fieldMap as $from => $to) {
                    if (isset($data[$from])) {
                        $data[$to] = $data[$from];
                        unset($data[$from]);
                    }
                }

                if (isset($data['gallery_images'])) {
                    $gallery = array_values(array_filter(array_map('trim', explode('|', (string) $data['gallery_images']))));
                    $data['additional_images'] = $gallery ?: null;
                    unset($data['gallery_images']);
                }

                Inventory::updateOrCreate(
                    ['model_number' => $data['model_number']],
                    $data + ['currency' => 'INR', 'last_synced_at' => now()]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        session()->forget('import_' . $request->import_id);

        if ($imported > 0) {
            Cache::forget('filter_options');
        }

        return redirect()->route('admin.bulk-import.index')
            ->with('success', "Imported {$imported} products successfully.")
            ->with('import_errors', $errors);
    }
}
