<?php

namespace App\Http\Controllers;

use App\Models\Inventory;

class ProductController extends Controller
{
    public function show(Inventory $inventory)
    {
        if (!$inventory->is_active) {
            abort(404);
        }

        $inventory->load('brand:id,name,slug,logo_url');

        $related = Inventory::with('brand:id,name')
            ->where('is_active', true)
            ->where('id', '!=', $inventory->id)
            ->where(function ($q) use ($inventory) {
                $q->where('category', $inventory->category)
                    ->orWhere('frame_shape', $inventory->frame_shape)
                    ->orWhere('brand_id', $inventory->brand_id);
            })
            ->latest('updated_at')
            ->take(4)
            ->get();

        return view('product', ['product' => $inventory, 'related' => $related]);
    }
}