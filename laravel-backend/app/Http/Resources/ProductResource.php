<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'shortDescription' => $this->short_description,
            'description' => $this->description,
            'isVisible' => $this->is_visible,
            'isFeatured' => $this->is_featured,
            'sortOrder' => $this->sort_order,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => $this->whenLoaded('tags', fn() => $this->tags->pluck('name')->toArray()),
            'images' => $this->whenLoaded('media', function () {
                return $this->getMedia('images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'alt' => $media->getCustomProperty('alt') ?: $this->name,
                        'isCover' => $media->getCustomProperty('is_cover') === true,
                        'width' => $media->getCustomProperty('width'),
                        'height' => $media->getCustomProperty('height'),
                        'conversions' => [
                            'thumb' => $media->getUrl('thumb'),
                            'cover' => $media->getUrl('cover'),
                        ],
                    ];
                });
            }),
            'specs' => $this->specs,
            'relatedProducts' => ProductResource::collection($this->whenLoaded('relatedProducts')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}