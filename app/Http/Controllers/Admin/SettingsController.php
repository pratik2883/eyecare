<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'nullable|string|max:100',
            'store_tagline' => 'nullable|string|max:100',
            'app_name' => 'nullable|string|max:100',
            'section_categories_title' => 'nullable|string|max:100',
            'section_offers_title' => 'nullable|string|max:100',
            'section_collection_title' => 'nullable|string|max:100',
            'trust_1_title' => 'nullable|string|max:100',
            'trust_1_text' => 'nullable|string|max:200',
            'trust_2_title' => 'nullable|string|max:100',
            'trust_2_text' => 'nullable|string|max:200',
            'trust_3_title' => 'nullable|string|max:100',
            'trust_3_text' => 'nullable|string|max:200',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value);
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Storefront settings saved.');
    }
}