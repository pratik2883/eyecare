<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::orderBy('sort_order')->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'nullable|string|max:500',
            'background_gradient' => 'nullable|string|max:255',
            'tag_text' => 'nullable|string|max:100',
            'link_url' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        if (is_null($data['sort_order'])) {
            $data['sort_order'] = Promo::max('sort_order') + 1;
        }

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::url($request->file('image')->store('promos', 'public'));
        }

        Promo::create($data);
        return redirect()->route('admin.promos.index')->with('success', 'Promo created.');
    }

    public function update(Request $request, Promo $promo)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'nullable|string|max:500',
            'background_gradient' => 'nullable|string|max:255',
            'tag_text' => 'nullable|string|max:100',
            'link_url' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($promo->image_url && str_contains($promo->image_url, '/storage/promos/')) {
                $oldPath = str_replace('/storage/', '', $promo->image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $data['image_url'] = Storage::url($request->file('image')->store('promos', 'public'));
        }

        $promo->update($data);
        return redirect()->route('admin.promos.index')->with('success', 'Promo updated.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return redirect()->route('admin.promos.index')->with('success', 'Promo deleted.');
    }
}
