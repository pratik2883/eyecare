<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(string $category)
    {
        $category = Category::where('slug', $category)->where('is_active', true)->first();

        if (!$category) {
            abort(404);
        }

        return view('category', [
            'categorySlug' => $category->slug,
            'categoryTitle' => $category->name,
            'categorySubtitle' => $category->description,
            'categoryIcon' => $category->icon,
        ]);
    }
}