<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

class Product extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia, HasTags;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'is_visible',
        'is_featured',
        'sort_order',
        'specs',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'specs' => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relations
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('cover')
            ->width(1200)
            ->height(800)
            ->sharpen(10)
            ->optimize()  
            ->nonQueued();
    }

    // Scopes
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeWithCategory($query, $categorySlugOrId)
    {
        return $query->whereHas('categories', function ($q) use ($categorySlugOrId) {
            if (is_numeric($categorySlugOrId)) {
                $q->where('categories.id', $categorySlugOrId);
            } else {
                $q->where('categories.slug', $categorySlugOrId);
            }
        });
    }

    public function scopeWithTags($query, array $tags)
    {
        return $query->withAnyTags($tags);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('short_description', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    // Helper methods
    public function getCoverImage(): ?Media
    {
        return $this->getMedia('images')
            ->first(fn($media) => $media->getCustomProperty('is_cover') === true);
    }

    public function setCoverImage(Media $media): void
    {
        // Remove is_cover from all other images
        $this->getMedia('images')->each(function ($m) {
            if ($m->id !== $media->id) {
                $m->setCustomProperty('is_cover', false);
                $m->save();
            }
        });

        // Set as cover
        $media->setCustomProperty('is_cover', true);
        $media->save();
    }
}