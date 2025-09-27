<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    public function created(Category $category): void
    {
        $this->clearCache();
    }

    public function updated(Category $category): void
    {
        $this->clearCache();
        Cache::tags(["category.{$category->id}"])->flush();
    }

    public function deleted(Category $category): void
    {
        $this->clearCache();
        Cache::tags(["category.{$category->id}"])->flush();
    }

    private function clearCache(): void
    {
        Cache::tags(['categories'])->flush();
    }
}