<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);

        // Clear cache when media is updated
        Media::observe(new class {
            public function updated(Media $media): void
            {
                if ($media->model_type === Product::class) {
                    \Cache::tags(['products', "product.{$media->model_id}"])->flush();
                }
            }

            public function deleted(Media $media): void
            {
                if ($media->model_type === Product::class) {
                    \Cache::tags(['products', "product.{$media->model_id}"])->flush();
                }
            }
        });
    }
}