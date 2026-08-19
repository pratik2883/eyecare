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
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_whatsapp' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
        ]);

        $socialKeys = [
            'social_facebook',
            'social_instagram',
            'social_youtube',
            'social_linkedin',
            'social_whatsapp',
            'social_twitter',
        ];

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value);
            } elseif (in_array($key, $socialKeys, true)) {
                Setting::set($key, '');
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Storefront settings saved.');
    }
}