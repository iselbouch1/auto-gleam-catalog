<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->clearCache();
    }

    public function updated(Product $product): void
    {
        $this->clearCache();
        Cache::tags(["product.{$product->id}"])->flush();
    }

    public function deleted(Product $product): void
    {
        $this->clearCache();
        Cache::tags(["product.{$product->id}"])->flush();
    }

    private function clearCache(): void
    {
        Cache::tags(['products'])->flush();
    }
}