<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::featured()->get(['id', 'name', 'slug', 'logo_url', 'sort_order']);
        return response()->json(['data' => $brands]);
    }

    public function topBrands(): JsonResponse
    {
        $brands = Brand::featured()->take(10)->get(['id', 'name', 'slug', 'logo_url']);
        return response()->json(['data' => $brands]);
    }
}
