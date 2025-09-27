<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'has_next' => $this->hasMorePages(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }
}