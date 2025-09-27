<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'categories.' . md5($request->getQueryString() ?? '');
        
        return Cache::tags(['categories'])->remember($cacheKey, 3600, function () use ($request) {
            $query = Category::query();

            // Filtre par visibilité (default: visible only)
            $visible = $request->boolean('visible', true);
            if ($visible !== null) {
                $query->where('is_visible', $visible);
            }

            // Filtre par parent
            if ($request->has('parent')) {
                $parent = $request->get('parent');
                if (is_numeric($parent)) {
                    $query->where('parent_id', $parent);
                } else {
                    $parentCategory = Category::where('slug', $parent)->first();
                    $query->where('parent_id', $parentCategory?->id);
                }
            }

            $categories = $query->ordered()->get();

            return CategoryResource::collection($categories);
        });
    }

    public function show(Category $category)
    {
        $cacheKey = "category.{$category->slug}";
        
        return Cache::tags(['categories', "category.{$category->id}"])->remember($cacheKey, 3600, function () use ($category) {
            $category->load('children');
            return new CategoryResource($category);
        });
    }
}