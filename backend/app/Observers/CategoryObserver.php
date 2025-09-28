<?php

namespace App\Observers;

use App\Events\CategoryUpdated;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    public function created(Category $category): void
    {
        $this->clearCache();
        broadcast(new CategoryUpdated($category, 'created'));
    }

    public function updated(Category $category): void
    {
        $this->clearCache();
        broadcast(new CategoryUpdated($category, 'updated'));
    }

    public function deleted(Category $category): void
    {
        $this->clearCache();
        broadcast(new CategoryUpdated($category, 'deleted'));
    }

    private function clearCache(): void
    {
        Cache::tags(['categories'])->flush();
    }
}