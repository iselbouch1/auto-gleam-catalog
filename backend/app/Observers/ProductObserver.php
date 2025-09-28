<?php

namespace App\Observers;

use App\Events\ProductUpdated;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->clearCache();
        broadcast(new ProductUpdated($product, 'created'));
    }

    public function updated(Product $product): void
    {
        $this->clearCache();
        broadcast(new ProductUpdated($product, 'updated'));
    }

    public function deleted(Product $product): void
    {
        $this->clearCache();
        broadcast(new ProductUpdated($product, 'deleted'));
    }

    private function clearCache(): void
    {
        Cache::tags(['products'])->flush();
    }
}