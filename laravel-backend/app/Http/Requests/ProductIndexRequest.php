<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'tags' => 'sometimes|string|max:500',
            'visible' => 'sometimes|boolean',
            'featured' => 'sometimes|boolean',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:60',
            'sort' => 'sometimes|in:name_asc,name_desc,recent',
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.max' => 'Le nombre d\'éléments par page ne peut pas dépasser 60.',
            'sort.in' => 'Le tri doit être: name_asc, name_desc ou recent.',
        ];
    }
}