<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(ProductIndexRequest $request)
    {
        $cacheKey = 'products.' . md5($request->getQueryString() ?? '');
        
        return Cache::tags(['products'])->remember($cacheKey, 3600, function () use ($request) {
            $query = Product::with(['categories', 'tags', 'media']);

            // Filtre par visibilité (default: visible only)
            if ($request->filled('visible')) {
                $query->where('is_visible', $request->boolean('visible'));
            } else {
                $query->visible();
            }

            // Filtre featured
            if ($request->boolean('featured')) {
                $query->featured();
            }

            // Filtre par catégorie
            if ($request->filled('category')) {
                $query->withCategory($request->get('category'));
            }

            // Filtre par tags
            if ($request->filled('tags')) {
                $tags = explode(',', $request->get('tags'));
                $query->withTags($tags);
            }

            // Recherche
            if ($request->filled('search')) {
                $query->search($request->get('search'));
            }

            // Tri
            $sort = $request->get('sort', 'name_asc');
            match ($sort) {
                'name_desc' => $query->orderBy('name', 'desc'),
                'recent' => $query->orderBy('created_at', 'desc'),
                default => $query->orderBy('name', 'asc'),
            };

            $perPage = min($request->integer('per_page', 24), 60);
            $products = $query->paginate($perPage);

            return new ProductCollection($products);
        });
    }

    public function show(Product $product)
    {
        $cacheKey = "product.{$product->slug}";
        
        return Cache::tags(['products', "product.{$product->id}"])->remember($cacheKey, 3600, function () use ($product) {
            $product->load(['categories', 'tags', 'media']);
            
            // Charger les produits associés (même catégorie)
            $relatedProducts = Product::visible()
                ->whereHas('categories', function ($q) use ($product) {
                    $q->whereIn('categories.id', $product->categories->pluck('id'));
                })
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();

            $product->setRelation('relatedProducts', $relatedProducts);

            return new ProductResource($product);
        });
    }
}